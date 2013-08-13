<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @copyright  Copyright (c) 2013 das MedienKombinat Gmbh <kontakt@das-medienkombinat.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */

/**
 * Product Sync Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Product extends Mage_Core_Model_Abstract
{
    /**
     * fraisr admin helper
     * @var Fraisr_Connect_Helper_Adminhtml_Data
     */
    protected $adminHelper = null;

    /**
     * collection of new added products (sku + fraisr_id)
     * 
     * @var array
     */
    protected $newProductsReport = array();

    /**
     * collection of updated products (sku + fraisr_id)
     * 
     * @var array
     */
    protected $updatedProductsReport = array();

    /**
     * collection of deleted products (sku + fraisr_id)
     * 
     * @var array
     */
    protected $deletedProductsReport = array();

    /**
     * collection of transmission failed products (sku + optional fraisr_id)
     * 
     * @var array
     */
    protected $failedProductsReport = array();

    /**
     * @const SYNCHRONISATION_ITERATIONS Synchronisation iterations per product
     */
    const SYNCHRONISATION_ITERATIONS = 3;

    /**
     * Synchronize product data
     * 
     * @return void
     */
    public function synchronize()
    {
        try {
            //$productsToSynchronize = Mage::helper('fraisrconnect/synchronisation_product')->getProductsToSynchronize();

            //Synchronize new products and  products to update
            $this->synchronizeNewAndUpdateProducts();
            
            //Synchronize products to delete (by existing products)
            $this->synchronizeDeleteProducts();

            //Synchronize products to delete (by deleted products from the queue)
            $this->synchronizeDeleteProductsByQueue();
        } catch (Fraisr_Connect_Exception $e) {
            $this->getAdminHelper()->logAndAdminOutputException(
                $this->getAdminHelper()->__(
                    'Product synchronisation aborted with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_PRODUCT_SYNC,
                $e
            );
        } catch (Exception $e) {
            $this->getAdminHelper()->logAndAdminOutputException(
                $this->getAdminHelper()->__(
                    'An unknown error during product synchronisation happened with message: "%s"',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_PRODUCT_SYNC,
                $e
            );
        }

        //Output product synchronisation report
        $this->outputSynchronisationReport();
    }

    /**
     * Synchronize new products
     * 
     * @return void
     */
    public function synchronizeNewAndUpdateProducts()
    {
        //Get product collection
        $newFraisrProducts = Mage::helper('fraisrconnect/synchronisation_product')->getNewAndUpdateFraisrProducts();

        //For every product
        foreach ($newFraisrProducts as $product) {
            try {
                //Update product
                if (false === is_null($product->getFraisrId())) {
                    $this->requestUpdateProduct($product);
                }

                //New product
                if (true === is_null($product->getFraisrId())) {
                    $this->requestNewProduct($product);
                }
            } catch (Fraisr_Connect_Model_Api_Exception $e) {
                //Add sku to failed products list
                $this->failedProductsReport[] = array(
                    'sku' => $product->getSku(),
                    'fraisr_id' => $product->getFraisrId(),
                    'error_message' => $e->getMessage(),
                    'task' => 'create/update'
                );
            }
        }
    }

    /**
     * Trigger create product request and save fraisrId
     * 
     * @param  Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function requestNewProduct($product)
    {
        $fraisrProductRequestData = $this->buildFaisrProductRequestData($product);

        $reponse = Mage::getModel('fraisrconnect/api_request')->requestPost(
            Mage::getModel('fraisrconnect/config')->getProductApiUri(),
            $fraisrProductRequestData
        );
        
        //Throw error in case that the fraisr_id was not transmitted
        if (false === isset($reponse["_id"])) {
            throw new Fraisr_Connect_Model_Api_Exception(
                $this->getAdminHelper()->__(
                    'FraisrId was not given for new product request and sku "%s".',
                    $product->getSku()
                )
            );
        }

        //Save FraisrId
        $product->setFraisrId($reponse['_id'])->save();

        //Add sku to success list
        $this->newProductsReport[] = array(
            'sku' => $product->getSku(),
            'fraisr_id' => $reponse['_id']
        );
    }

    /**
     * Trigger update product request and save fraisrId
     * 
     * @param  Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function requestUpdateProduct($product)
    {
        $fraisrProductRequestData = $this->buildFaisrProductRequestData($product);

        $reponse = Mage::getModel('fraisrconnect/api_request')->requestPut(
            Mage::getModel('fraisrconnect/config')->getProductApiUri(
                $product->getFraisrId()
            ),
            $fraisrProductRequestData
        );

        //Add sku to success list
        $this->updatedProductsReport[] = array(
            'sku' => $product->getSku(),
            'fraisr_id' => $product->getFraisrId()
        );
    }

    /**
     * Build fraisr product request data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function buildFaisrProductRequestData($product)
    {
        $requestData = array(
            'internalid'    => $product->getSku(),
            'name'          => $product->getName(),
            'description'   => strip_tags($product->getDescription()),
            'category'      => $product->getFraisrCategory(),
            'url'           => $product->getProductUrl(false),
            'cause'         => $product->getFraisrCause(),
            'donation'      => $product->getFraisrDonationPercentage(),
            'qty'           => (int) Mage::getModel('cataloginventory/stock_item')
                                   ->loadByProduct($product)
                                   ->getQty(),
        );

        //Calculate prices
        $prices = Mage::helper('fraisrconnect/synchronisation_product')->calculatePrices($product);
        $requestData["price"] = $prices["price"];
        if ($prices["special_price"] > 0) {
            $requestData["special_price"] = $prices["special_price"];
        } else {
            $requestData["special_price"] = '';
        }

        /**
         * Retrieve images
         */
        //Main Image
        if (false === is_null($product->getSmallImage())) {
            $requestData["images"][] = 
                Mage::app()
                    ->getStore(
                        Mage::getModel('fraisrconnect/config')->getCatalogExportStoreId()
                    )
                    ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                    .'catalog/product'.$product->getImage();
        }

        //Gallery Images
        $product->load('media_gallery');
        $productImages = $product->getMediaGalleryImages();
        if ($productImages) {
            foreach ($productImages as $image) {
                $requestData["images"][] = $image->getUrl();
            }
        }
        return $requestData;
    }

    /**
     * Output product synchronisation report
     *
     * Write admin notification messages
     * And generate an overview log entry
     * 
     * @return void
     */
    protected function outputSynchronisationReport()
    {
        //Add admin notice message about new added products
        $newProductsMessage = $this->getAdminHelper()->__(
            '%s product(s) were successfully added to fraisr.',
            (int) count($this->newProductsReport)
        );
        Mage::getSingleton('adminhtml/session')->addNotice($newProductsMessage);

        //Add admin notice message about updated products
        $updatedProductsMessage = $this->getAdminHelper()->__(
            '%s product(s) were successfully updated in fraisr.',
            (int) count($this->updatedProductsReport)
        );
        Mage::getSingleton('adminhtml/session')->addNotice($updatedProductsMessage);

        //Add admin notice message about deleted products
        $deletedProductsMessage = $this->getAdminHelper()->__(
            '%s product(s) were successfully deleted from fraisr.',
            (int) count($this->deletedProductsReport)
        );
        Mage::getSingleton('adminhtml/session')->addNotice($deletedProductsMessage);

        //Add admin notice message about transmission failed products
        $failedProductsMessage = $this->getAdminHelper()->__(
            'The transmission of %s product(s) failed during fraisr synchronisation.',
            (int) count($this->failedProductsReport)
        );
        Mage::getSingleton('adminhtml/session')->addNotice($failedProductsMessage);

        //Write detailed log report
        $logMessage = sprintf(
            "%s\nDetails: %s\n\n"
            ."%s\nDetails: %s\n\n"
            ."%s\nDetails: %s\n\n"
            ."%s\nDetails: %s\n\n",
            $newProductsMessage, var_export($this->newProductsReport, true),
            $updatedProductsMessage, var_export($this->updatedProductsReport, true),
            $deletedProductsMessage, var_export($this->deletedProductsReport, true),
            $failedProductsMessage, var_export($this->failedProductsReport, true)
        );
        Mage::getModel('fraisrconnect/log')
            ->setTitle($this->getAdminHelper()->__('Product synchronisation report'))
            ->setMessage($logMessage)
            ->setTask(Fraisr_Connect_Model_Log::LOG_TASK_PRODUCT_SYNC)
            ->logNotice();
    }

    /**
     * Get admin helper
     * 
     * @return Fraisr_Connect_Helper_Adminhtml_Data
     */
    protected function getAdminHelper()
    {
        if (true === is_null($this->adminHelper)) {
            $this->adminHelper = Mage::helper('fraisrconnect/adminhtml_data');
        }
        return $this->adminHelper;
    }

    /**
     * Synchronize products to delete 
     * 
     * Flagged with 'Fraisr':'No' but fraisr_id existing
     * 
     * @return void
     */
    public function synchronizeDeleteProducts()
    {
        //Get product collection (Flagged with 'Fraisr':'No' but fraisr_id existing)
        $deleteFraisrProducts = Mage::helper('fraisrconnect/synchronisation_product')->getDeleteFraisrProducts();

        //For every product
        foreach ($deleteFraisrProducts as $product) {
            try {
                //Trigger delete request
                $this->requestDeleteProduct($product->getFraisrId(), $product->getSku());

                //Unset fraisr_id
                $product->setFraisrId(null)->save();
            } catch (Fraisr_Connect_Model_Api_Exception $e) {
                //Add sku to delete products list
                $this->failedProductsReport[] = array(
                    'sku' => $product->getSku(),
                    'fraisr_id' => $product->getFraisrId(),
                    'error_message' => $e->getMessage(),
                    'task' => 'delete'
                );
            }
        }
    }

    /**
     * Synchronize products to delete 
     * 
     * From delete queue
     * 
     * @return void
     */
    public function synchronizeDeleteProductsByQueue()
    {
        //Get product collection from delete queue
        $deleteFraisrProducts = Mage::getModel('fraisrconnect/config')->getProductsFromDeleteQueue();

        //For every product
        foreach ($deleteFraisrProducts as $product) {
            try {
                //Trigger delete request
                $this->requestDeleteProduct(
                    $product['fraisr_id'],
                    $product['sku']
                );

                //Remove product from delete queue
                Mage::getModel('fraisrconnect/config')->removeProductFromDeleteQueue($product['sku']);
            } catch (Fraisr_Connect_Model_Api_Exception $e) {
                //Add sku to delete products list
                $this->failedProductsReport[] = array(
                    'sku' => $product['sku'],
                    'fraisr_id' => $product['fraisr_id'],
                    'error_message' => $e->getMessage(),
                    'task' => 'delete'
                );
            }
        }
    }

    /**
     * Trigger delete product request and unset fraisrId
     * 
     * @param string $fraisrId
     * @param string $sku
     * @return void
     */
    protected function requestDeleteProduct($fraisrId, $sku = '')
    {
        $reponse = Mage::getModel('fraisrconnect/api_request')->requestDelete(
            Mage::getModel('fraisrconnect/config')->getProductApiUri($fraisrId)
        );

        //Add sku to delete list
        $this->deletedProductsReport[] = array(
            'sku' => $sku,
            'fraisr_id' => $fraisrId
        );
    }

    /**
     * Mark products as to synchronize
     * 
     * @return void
     */
    public function markProductsAsToSynchronize()
    {
        try {
            //Get product collection of new and update products
            $newAndUpdateFraisrProducts = Mage::helper('fraisrconnect/synchronisation_product')->getNewAndUpdateFraisrProducts();

            //Mark as to synchronize
            Mage::helper('fraisrconnect/synchronisation_product')->markProductCollectionAsToSynchronize(
                $newAndUpdateFraisrProducts
            );
            //Success Message
            $this->getAdminHelper()->logAndAdminOutputSuccess(
                $this->getAdminHelper()->__(
                    '%s product(s) were successfully marked as to synchronize (create/update) to fraisr.',
                    count($newAndUpdateFraisrProducts)
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );

            //Get product collection of to delete products
            $toDeleteProducts = Mage::helper('fraisrconnect/synchronisation_product')->getDeleteFraisrProducts();

            //Mark as to synchronize
            Mage::helper('fraisrconnect/synchronisation_product')->markProductCollectionAsToSynchronize(
                $toDeleteProducts
            );
            //Success Message
            $this->getAdminHelper()->logAndAdminOutputSuccess(
                $this->getAdminHelper()->__(
                    '%s product(s) were successfully marked as to synchronize (delete) to fraisr.',
                    count($toDeleteProducts)
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );

            //Get product collection of to delete products (from queue)
            $toDeleteProductsByQueue = Mage::getModel('fraisrconnect/config')->getProductsFromDeleteQueue();
            //Notice Message
            $this->getAdminHelper()->logAndAdminOutputNotice(
                $this->getAdminHelper()->__(
                    '%s product(s) are waiting in the queue to be deleted in fraisr.',
                    count($toDeleteProductsByQueue)
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Exception $e) {
            $this->getAdminHelper()->logAndAdminOutputException(
                $this->getAdminHelper()->__(
                    'An unknown error happened during mark products to synchronisation action with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_PRODUCT_SYNC,
                $e
            );
        }
    }
}
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
     * Synchronize product data
     * 
     * @return void
     */
    public function synchronize()
    {
        $helper = Mage::helper('fraisrconnect/adminhtml_data');

        try {
            //Synchronize new products and  products to update
            $this->synchronizeNewAndUpdateProducts();
            
            /**
             * Synchronize products to delete 
             * 
             * Flagged with 'Fraisr':'No' but fraisr_id existing
             * && From delete queue
             */
            $this->synchronizeDeleteProducts();
        } catch (Fraisr_Connect_Model_Api_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Product synchronisation failed during API request with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC,
                $e
            );
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Product synchronisation failed with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC,
                $e
            );
        } catch (Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'An unknown error during product synchronisation happened with message: "%s"',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC,
                $e
            );
        }
    }

    /**
     * Synchronize new products
     * 
     * @return void
     */
    public function synchronizeNewAndUpdateProducts()
    {
        $helper = Mage::helper('fraisrconnect/adminhtml_data');

        //Get product collection
        $newFraisrProducts = $this->getNewAndUpdateFraisrProducts();

        //For every product
        foreach ($newFraisrProducts as $product) {
            try {
                $fraisrProductRequestData = $this->buildFaisrProductRequestData($product);

                //New product
                if (true === is_null($product->getFraisrId())) {
                    $this->requestNewProduct($product);
                }
                
                //Update product
                if (false === is_null($product->getFraisrId())) {
                    $this->requestUpdateProduct($product);
                }
            } catch (Exception $e) {
                //TODO
                //Exception Handling for every product add/update or delete task
                throw $e;
            }
        }
        
        exit("end synchronizeNewAndUpdateProducts");
    }

    /**
     * Trigger create product request and save fraisrId
     * 
     * @param  Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function requestNewProduct($product)
    {
        $helper = Mage::helper('fraisrconnect/adminhtml_data');

        $reponse = Mage::getModel('fraisrconnect/api_request')->requestPost(
            Mage::getModel('fraisrconnect/config')->getProductApiUri(),
            $fraisrProductRequestData
        );
        
        //Save FraisrId
        if (false === isset($reponse["_id"])) {
            throw new Fraisr_Connect_Model_Api_Exception(
                $helper->__(
                    'FraisrId was not given for new product request and sku "%s".',
                    $product->getSku()
                )
            );
        }
        $product->setFraisrId($reponse["_id"])->save();
    }

    /**
     * Trigger update product request and save fraisrId
     * 
     * @param  Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function requestUpdateProduct($product)
    {
        $helper = Mage::helper('fraisrconnect/adminhtml_data');

        $reponse = Mage::getModel('fraisrconnect/api_request')->requestPut(
            Mage::getModel('fraisrconnect/config')->getProductApiUri(
                $product->getFraisrId()
            ),
            $fraisrProductRequestData
        );
    }


    /**
     * Get new and update fraisr products
     *
     * 1.) fraisr_enabled:yes
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getNewAndUpdateFraisrProducts()
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter(Mage::getModel('fraisrconnect/config')->getCatalogExportStoreId())
            ->addFieldToFilter('fraisr_enabled', 1); //Only products which are enabled for Fraisr sync
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
}
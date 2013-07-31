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

        //Synchronize new products
        ////Synchronize products to update
        $this->synchronizeNewAndUpdateProducts();
        
        //Synchronize products to delete (Flagged with 'Fraisr':'No' but fraisr_id existing)
        
        //Synchronize products to delete (From delete queue)

        try {

        } catch (Fraisr_Connect_Model_Api_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Cause synchronisation failed during API request with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Cause synchronisation failed with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'An unknown error during cause synchronisation happened with message: "%s"',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
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
        //Get product collection
        $newFraisrProducts = $this->getNewAndUpdateFraisrProducts();

        //For every product
        foreach ($newFraisrProducts as $product) {
            try {
                //var_dump($newFraisrProducts->getFirstItem()->getData());
                $fraisrProductRequestData = $this->buildFaisrProductRequestData($product);

                //New product
                if (true === is_null($product->getFraisrId())) {

                }
                
                //Update product
                if (false === is_null($product->getFraisrId())) {
                    
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        exit("end synchronizeNewAndUpdateProducts");
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
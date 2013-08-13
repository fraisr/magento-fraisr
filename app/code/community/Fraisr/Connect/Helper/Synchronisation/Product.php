
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
 * Product Synchronisation Helper
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Helper_Synchronisation_Product extends Mage_Core_Helper_Abstract
{
    /**
    * Calculate price and special price information
    * 
    * @param Mage_Catalog_Model_Product $product
    * @return array
    */
    public function calculatePrices($product)
    {
        $prices = array();

        //active special price
        if ($product->getPrice() > $product->getFinalPrice()) {
            $prices["special_price"] = round($product->getFinalPrice(), 2);
            $prices["price"] = round($product->getPrice(), 2);
        } else { //normal price
            $prices["special_price"] = "";
            $prices["price"] = round($product->getFinalPrice(), 2);
        }
        return $prices;
    }

    /**
     * Mark product collection as to synchronize
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @return void
     */
    public function markProductCollectionAsToSynchronize($products)
    {
        foreach ($products as $product) {
            $product
                ->setFraisrUpdate(Fraisr_Connect_Model_Product::SYNCHRONISATION_ITERATIONS)
                ->getResource()
                ->saveAttribute($product, 'fraisr_update');
        }
    }

    /**
     * Get to delete fraisr products
     *
     * 1.) fraisr_enabled:no + fraisr_id existing
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getDeleteFraisrProducts()
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter(Mage::getModel('fraisrconnect/config')->getCatalogExportStoreId())
            ->addFieldToFilter('fraisr_enabled', 0)
            ->addFieldToFilter('fraisr_id', array('notnull' => true));
    }

    /**
     * Get new and update fraisr products
     *
     * 1.) fraisr_enabled:yes
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getNewAndUpdateFraisrProducts()
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter(Mage::getModel('fraisrconnect/config')->getCatalogExportStoreId())
            ->addFieldToFilter('fraisr_enabled', 1); //Only products which are enabled for Fraisr sync
    }

    /**
     * Get products which as marked as to sychronize
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductsToSynchronize()
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter(Mage::getModel('fraisrconnect/config')->getCatalogExportStoreId())
            ->addFieldToFilter(
                'fraisr_update',
                array('gt' => 0)
            ); //Only products which are marked as to update (iterations > 0)
    }

    /**
     * Mark a product as synchronized
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    public function markAsSynchronized($product)
    {
        $product
            ->setFraisrUpdate(0)
            ->getResource()
            ->saveAttribute($product, 'fraisr_update');
    }

    /**
     * Descrease the synchronisation iterations by 1
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    public function decreaseSyncIteration($product)
    {
        $fraisrUpdate = $product->getFraisrUpdate() - 1;
        if ($fraisrUpdate < 0) {
            $fraisrUpdate = 0;
        }
        $product
            ->setFraisrUpdate($fraisrUpdate)
            ->getResource()
            ->saveAttribute($product, 'fraisr_update');
    }

    /**
     * Build syncronisation report details
     * 
     * @param array $report
     * @return string
     */
    public function buildSyncReportDetails($reports)
    {
        //Return empty string if report is empty
        if (0 == count($reports)) {
            return '';
        }

        //Build message
        $reportMessage = '';
        foreach ($reports as $report) {
            foreach ($report as $key => $value) {
                $reportMessage .= sprintf('["%s":"%s"]', $key, $value);
            }
            $reportMessage .= "\n";
        }
        return $reportMessage;
    }
}

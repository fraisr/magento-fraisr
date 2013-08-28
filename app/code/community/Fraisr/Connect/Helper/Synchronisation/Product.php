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
class Fraisr_Connect_Helper_Synchronisation_Product extends Fraisr_Connect_Helper_Synchronisation_Abstract
{
    /**
     * @const PRODUCT_MIN_QTY Minimum product qty in case it is 0 or can't be estimated
     */
    const PRODUCT_MIN_QTY = 1;

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
     * Check if 'is_start_price'-flag should be set
     * 
     * @param  Mage_Catalog_Model_Product $product
     * @return int
     */
    public function getIsStartPrice($product)
    {
        /**
         * Return 1 only if product type is bundle or configurable
         * This is of course no real check about is_start_price but a legal issue
         */
        if ('configurable' == $product->getTypeId() || 'bundle' == $product->getTypeId()) {
            return 1;
        } else {
            return 0;
        }
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
     * Create a new product synchronisation cron task to continue the previous one (+x minutes)
     *
     * @param Mage_Cron_Model_Schedule $observer
     * @return Mage_Cron_Core_Schedule
     */
    public function createProductSyncCronTask($observer)
    {
        //Check if the $observer consists the necessary data and has the correct jobCode
        if ('fraisrconnect_synchronisation_products' != $observer->getJobCode()) {
            throw new Fraisr_Connect_Exception(
                $this->__('Observer job code is missing.')
            );
        }

        //Create new cron schedule
        $schedule = Mage::getModel('cron/schedule');
        $schedule
            ->setJobCode($observer->getJobCode())
            ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
            ->setScheduledAt(
                date(
                    'Y-m-d H:i:s',
                    strtotime($schedule->getScheduledAt(). ' + '.self::CRON_TASK_ADDITIONAL_MINUTES.' minutes')
                )
            )
            ->setMessages($this->__(
                'This schedule was created to continue %s (schedule_id)',
                $observer->getScheduleId())
            )
            ->save();

        return $schedule;
    }

    /**
     * Get product qty
     *
     * If qty = 0 (for configurable and bundle products) set "1" by default
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    public function getProductQty($product)
    {
        //If product type is "configurable" or "bundle" return the min qty
        if ('configurable' == $product->getTypeId() || 'bundle' == $product->getTypeId()) {
            return self::PRODUCT_MIN_QTY;
        }

        //Get regular product qty
        $qty = (int) Mage::getModel('cataloginventory/stock_item')
                                   ->loadByProduct($product)
                                   ->getQty();

        //If qty is < 0, return the min qty too
        if ($qty < self::PRODUCT_MIN_QTY) {
            $qty = self::PRODUCT_MIN_QTY;
        }

        return (int) $qty;
    }
}
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
 * Observer
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Observer
{
    /**
     * Initiate cause synchronisation
     * & check for products with non-existing causes
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function synchronizeCauses($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(false)) {
            return;
        }

        //Retrieve and save causes
        Mage::getModel('fraisrconnect/cause')->synchronize();

        /**
         * Check if products exists which causes doesn't exist anymore
         * If some were find, set 'fraisr_enabled' to false
         */
        Mage::getModel('fraisrconnect/cause')->productCheck();
    }

    /**
     * Initiate category synchronisation
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function synchronizeCategories($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(false)) {
            return;
        }
        Mage::getModel('fraisrconnect/category')->synchronize();
    }

    /**
     * Initiate product synchronisation
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function synchronizeProducts($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(false)) {
            return;
        }

        //Trigger product synchronisation
        $productSyncronisation = Mage::getModel('fraisrconnect/product');
        $productSyncronisation->synchronize();

        try {
            //Check if product synchronisation is complete, if not add a next cronjob task manually
            if (false === $productSyncronisation->isSynchronisationComplete()) {
                //Add a next cronjob task
                $cronTask = Mage::helper('fraisrconnect/synchronisation_product')->createProductSyncCronTask(
                    $observer
                );
                
                //Log about adding a next cronjob task
                $logTitle = Mage::helper('fraisrconnect/data')->__(
                    'Not all products have been synchronized because of a transmission error or a script timeout. Therefore another cron task was added for GMT-Datetime %s.',
                    $cronTask->getScheduledAt()
                );
                Mage::getModel('fraisrconnect/log')
                    ->setTitle($logTitle)
                    ->setTask(Fraisr_Connect_Model_Log::LOG_TASK_PRODUCT_SYNC)
                    ->logNotice();
            }
        } catch (Exception $e) {
            //Log error title
            $logTitle = Mage::helper('fraisrconnect/data')->__(
                'An error occured during the creation of the following cron task for the product sychronisation with message: "%s".',
                $e->getMessage()
            );

            //Error message that the next crontask may could not be added
            Mage::getModel('fraisrconnect/log')
                ->setTitle($logTitle)
                ->setTask(Fraisr_Connect_Model_Log::LOG_TASK_PRODUCT_SYNC)
                ->logError();
        }
    }

    /**
     * Initiate mark products for synchronisation action
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function markProductsAsToSynchronize($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            return;
        }
        Mage::getModel('fraisrconnect/product')->markProductsAsToSynchronize();
    }

    /**
     * Add a product to the fraisr delete queue
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function addProductToQueue($observer)
    {
        $product = $observer->getEvent()->getProduct();

        //Check if given product is valid and if the product has a fraisr_id
        if ($product instanceof Mage_Catalog_Model_Product
            && false === is_null($product->getFraisrId())) {
            //Add the product to the fraisr delete queue
            Mage::getModel('fraisrconnect/config')->addProductToDeleteQueue($product);
        }
    }

    /**
     * Add the fraisr_id to the fraisr_product_id element of the quote_item - object
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function addFraisrIdToQuoteItem($observer)
    {
        $event = $observer->getEvent();
        $quoteItem = $event->getQuoteItem();
        $quoteItem->setFraisrProductId($quoteItem->getProduct()->getFraisrId());
    }

    /**
     * Add the fraisr_id to the fraisr_product_id element of the order_item - object
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function addFraisrIdToOrderItem($observer)
    {
        $event = $observer->getEvent();
        $order_item = $event->getOrderItem();
        $order_item->setFraisrProductId($event->getItem()->getFraisrProductId());
    }
}
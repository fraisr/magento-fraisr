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
     * Current store code
     * @var String
     */
    protected $_currentStoreCode = null;

    /**
     * @constructor
     */
    public function __construct(){
        $this->_currentStoreCode = Mage::app()->getStore()->getCode();
    }

    /**
     * sets to default store
     */
    protected function setDefaultStore(){
        $websites = Mage::app()->getWebsites();
        $code = $websites[1]->getDefaultStore()->getCode();

        if(($store = Mage::getModel('fraisrconnect/config')->getCatalogExportStoreId()) !== 0){
            $code = Mage::app()->getStore($store)->getCode();
        }

        if($code === $this->_currentStoreCode)
            return;
        
        Mage::app()->setCurrentStore($code);
    }

    /**
     * resets store
     * @return [type] [description]
     */
    protected function resetStore(){
        Mage::app()->setCurrentStore($this->_currentStoreCode);
    }

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

        $this->setDefaultStore();

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

        $this->resetStore();
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

        $this->setDefaultStore();

        Mage::getModel('fraisrconnect/product')->markProductsAsToSynchronize();

        $this->resetStore();
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
     * Add the fraisr_id to the fraisr_product_id column of the quote_item - object
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function addFraisrIdToQuoteItem($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(false)) {
            return;
        }

        $event = $observer->getEvent();
        $quoteItem = $event->getQuoteItem();
        $product = $quoteItem->getProduct();

        if (false === is_null($product->getFraisrId())
            && 1 == $product->getFraisrEnabled()
            && false === is_null($product->getFraisrCause())
            && false === is_null($product->getFraisrDonationPercentage())) {
            $quoteItem->setFraisrProductId($product->getFraisrId());
            $quoteItem->setFraisrCauseId($product->getFraisrCause());
            $quoteItem->setFraisrDonationPercentage($product->getFraisrDonationPercentage());
        }
    }

    /**
     * Add the fraisr_id to the fraisr_product_id column of the order_item - object
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function addFraisrIdToOrderItem($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(false)) {
            return;
        }
        
        $event = $observer->getEvent();
        $order_item = $event->getOrderItem();
        $order_item->setFraisrProductId($event->getItem()->getFraisrProductId());
        $order_item->setFraisrCauseId($event->getItem()->getFraisrCauseId());
        $order_item->setFraisrDonationPercentage($event->getItem()->getFraisrDonationPercentage());
    }

    /**
     * Initiate order synchronisation
     *
     * @param  Mage_Cron_Model_Schedule $observer
     * @return void
     */
    public function synchronizeOrders($observer)
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(false)) {
            return;
        }

        //Trigger order synchronisation
        $orderSyncronisation = Mage::getModel('fraisrconnect/order');
        $orderSyncronisation->synchronize();

        try {
            //Check if order synchronisation is complete, if not add a next cronjob task manually
            if (false === $orderSyncronisation->isSynchronisationComplete()) {
                //Add a next cronjob task
                $cronTask = Mage::helper('fraisrconnect/synchronisation_order')->createOrderSyncCronTask(
                    $observer
                );
                
                //Log about adding a next cronjob task
                $logTitle = Mage::helper('fraisrconnect/data')->__(
                    'Not all orders have been synchronized because of a transmission error or a script timeout. Therefore another cron task was added for GMT-Datetime %s.',
                    $cronTask->getScheduledAt()
                );
                Mage::getModel('fraisrconnect/log')
                    ->setTitle($logTitle)
                    ->setTask(Fraisr_Connect_Model_Log::LOG_TASK_ORDER_SYNC)
                    ->logNotice();
            }
        } catch (Exception $e) {
            //Log error title
            $logTitle = Mage::helper('fraisrconnect/data')->__(
                'An error occured during the creation of the following cron task for the order sychronisation with message: "%s".',
                $e->getMessage()
            );

            //Error message that the next crontask may could not be added
            Mage::getModel('fraisrconnect/log')
                ->setTitle($logTitle)
                ->setTask(Fraisr_Connect_Model_Log::LOG_TASK_ORDER_SYNC)
                ->logError();
        }
    }

    /**
     * Makes an request to api/v1/connect with callback_url
     * @return void
     */
    public function connectToApi(){
        $logger = Mage::getModel("fraisrconnect/log");
        $config = Mage::getModel("fraisrconnect/config");
        $callback_url = Mage::getUrl("fraisrconnect");

        if($config->isActive() !== true)
            return;

        $this->setDefaultStore();

        try{
            $response = Mage::getModel('fraisrconnect/api_request')->requestPost(
                Mage::getModel('fraisrconnect/config')->getConnectApiUri(),
                compact("callback_url")
            );

            if($response["success"] !== true){
                if(array_key_exists($response["error"])){
                    throw new Exception($response["error"]);
                }

                throw new Exception("Unknown error occured:" . Zend_Json::encode($response));
            }

            $logger->setTitle("Connected to fraisr");
            $logger->setMessage("Successfully connected to fraisr.");
            $logger->logSuccess();
        }catch(Exception $error){
            $logger->setTitle("Connect Error");
            $logger->setMessage($error->getMessage());
            $logger->logError();
        }

        $this->resetStore();
    }

    /**
     * Adds an additional options which indivates this order item as a fraisr product
     * @param $observer
     * @return void
     */
    public function catalogProductLoadAfter($observer){
        $action = Mage::app()->getFrontController()->getAction();

        if(true === is_null($action))
            return;

        if($action->getFullActionName() !== "checkout_cart_add")
            return;

        $product = $observer->getProduct();

        if(is_null($product->getFraisrId()))
            return;

        if(is_null($product->getFraisrCause()))
            return;

        if(is_null($product->getFraisrDonationPercentage()))
            return;

        $additionalOptions = array();
        if($additionalOption = $product->getCustomOption("additional_options")){
            $additionalOptions = (array) unserialize($additionalOption->getValue());
        }

        $helper = Mage::helper('fraisrconnect/data');

        $additionalOptions[] = array(
            "label" => "fraisr",
            "value" => $helper->__("%s%% donation will go to %s", 
                $product->getFraisrDonationPercentage(),
                Mage::getModel('fraisrconnect/cause')->load($product->getFraisrCause())->getName()),
        );

        $product->addCustomOption("additional_options", serialize($additionalOptions));
    }

    /**
     * adds additional options from quote item to order item
     * @param $observer
     * @return void
     */
    public function salesConvertQuoteItemToOrderItem($observer){
        $quoteItem = $observer->getItem();

        if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $orderItem = $observer->getOrderItem();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
    }

    /**
     * Adding grid collumn "has fraisr items"
     * @param $observer
     * @return void
     */
    public function customgridColumnAppend($observer){
        $block = $observer->getBlock();

        if(!isset($block))
            return;

        if($block->getType() !== "adminhtml/sales_order_grid")
            return;

        $block->addColumnAfter("has_fraisr_items", array(
            "header" => Mage::helper("fraisrconnect/data")->__("Has fraisr items"),
            "index" => "has_fraisr_items",
            'type'  => "options",
            "width" => "70px",
            "options" => array("0" => "no", "1" => "yes"),
        ), "status");
    }

    /**
     * Set column has_fraisr_id to sales/order table
     * @param $observer
     * @return void
     */
    public function salesConvertQuoteToOrder($observer){
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $has_fraisr_items = 0;

        foreach($quote->getAllVisibleItems() AS $quoteItem){
            if(is_null($quoteItem->getFraisrProductId()))
                continue;

            $has_fraisr_items = 1;
            break;
        }
        
        $order->setHasFraisrItems($has_fraisr_items);
    }
}
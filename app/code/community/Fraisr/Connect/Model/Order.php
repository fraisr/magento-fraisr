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
 * Order Sync Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Order extends Mage_Core_Model_Abstract
{
    /**
     * fraisr admin helper
     * @var Fraisr_Connect_Helper_Adminhtml_Data
     */
    protected $adminHelper = null;

    /**
     * collection of new orders
     * 
     * @var array
     */
    protected $newOrdersReport = array();

    /**
     * collection of updated orders
     * 
     * @var array
     */
    protected $updatedOrdersReport = array();

    /**
     * collection of transmission failed orders
     * 
     * @var array
     */
    protected $failedOrdersReport = array();

    /**
     * Flag to check if the synchronisation is finished/completed or not
     * 
     * @var boolean
     */
    public $synchronisationFinished = false;

    /**
     * Synchronisation start time
     * 
     * @var int
     */
    public $synchronisationStartTime = null;

    /**
     * Synchronize order data
     * 
     * @return void
     */
    public function synchronize()
    {
        try {
            //Set syncronisation start time
            $this->synchronisationStartTime = time();

            //Synchronize orders
            $this->synchronizeOrders();

            //Set synchronisation as finished if runtime is not exceeded
            if (false === Mage::helper('fraisrconnect/synchronisation_product')
                        ->isRuntimeExceeded($this->synchronisationStartTime)) {
                $this->synchronisationFinished = true;
            }
        } catch (Fraisr_Connect_Exception $e) {
            $this->getAdminHelper()->logAndAdminOutputException(
                $this->getAdminHelper()->__(
                    'Order synchronisation aborted with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_ORDER_SYNC,
                $e
            );
        } catch (Exception $e) {
            $this->getAdminHelper()->logAndAdminOutputException(
                $this->getAdminHelper()->__(
                    'An unknown error during order synchronisation happened with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_ORDER_SYNC,
                $e
            );
        }

        //Output order synchronisation report
        //TODO REPORT
    }

    /**
     * Get order collection and loop through every order
     * 
     * @return void
     */
    public function synchronizeOrders()
    {
        $orderItemsToSynchronize = Mage::helper('fraisrconnect/synchronisation_order')
            ->getOrderItemsToSynchronize();

        //Loop through every order
        foreach ($orderItemsToSynchronize as $orderItem) {
            //Validate order/order_item
            if (false === $this->isOrderItemValid($orderItem)) {
                continue;
            }

            //New order
            if (true === is_null($orderItem->getFraisrOrderId())) {
                $this->requestNewOrder($orderItem);
                exit("new order");
            }

            //Update order
            if (false === is_null($orderItem->getFraisrOrderId())) {
                var_dump("update order");
            }
        }
    }

    /**
     * Check if the order_item is valid to be transferred to fraisr
     *
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return boolean
     */
    protected function isOrderItemValid($orderItem)
    {
        //Check if all necessary data for the transfer is existing
        if (true === is_null($orderItem->getFraisrProductId())
            || true === is_null($orderItem->getFraisrCauseId())
            || true === is_null($orderItem->getFraisrDonationPercentage())) {
            return false;
        }

        //Check if 'base_currency_code' or 'order_currency_code' is EUR 
        if ('EUR' !== $orderItem->getBaseCurrencyCode()
            && 'EUR' === $orderItem->getOrderCurrencyCode()) {
            return false;
        }

        return true;
    }

    /**
     * Prepare the data for the fraisr order create request
     * 
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return array
     */
    protected function prepareOrderRequestData($orderItem)
    {
        //Calculate price
        $price = 0;
        if ('EUR' === $orderItem->getBaseCurrencyCode()) {
            $price = $orderItem->getBasePriceInclTax();
        } elseif ('EUR' === $orderItem->getOrderCurrencyCode()) {
            $price = $orderItem->getPriceInclTax();
        }

        return array(
            'product' => $orderItem->getFraisrProductId(),
            'amount' => Mage::helper('fraisrconnect/synchronisation_order')->getOrderItemQty($orderItem),
            'price' => $price,
            'cause' => $orderItem->getFraisrCauseId(),
            'donation' => $orderItem->getFraisrDonationPercentage()
        );
    }

    /**
     * Trigger create order request and save fraisr_order_id
     * 
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return void
     */
    protected function requestNewOrder($orderItem)
    {
        $orderRequestData = $this->prepareOrderRequestData($orderItem);

        $reponse = Mage::getModel('fraisrconnect/api_request')->requestPost(
            Mage::getModel('fraisrconnect/config')->getOrderApiUri(),
            $orderRequestData
        );
        
        //Throw error in case that the fraisr_id was not transmitted
        if (false === isset($reponse["_id"])) {
            throw new Fraisr_Connect_Model_Api_Exception(
                $this->getAdminHelper()->__(
                    'FraisrOrderId was not given for new order request, order "%s" and item "%s".',
                    $orderItem->getIncrementId(),
                    $orderItem->getFraisrProductId()
                )
            );
        }

        //Save FraisrOrderId and FraisrQtyOrdered
        $orderItem
            ->setFraisrOrderId($reponse['_id'])
            ->setFraisrQtyOrdered($orderRequestData['amount'])
            ->save();

        //Add order_id to success list
        $this->newOrdersReport[] = array(
            'magento_order_id' => $orderItem->getIncrementId(),
            'fraisr_order_id' => $orderItem->getFraisrProductId()
        );
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
     * Check if synchronisation was complete
     * 
     * @return boolean
     */
    public function isSynchronisationComplete()
    {
        //Return false if the internal synchronisation flag is already marked as false
        if (false === $this->synchronisationFinished) {
            return false;
        }

        return true;
    }
}
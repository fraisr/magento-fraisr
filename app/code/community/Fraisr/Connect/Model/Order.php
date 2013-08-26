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
     * collection of updated orders
     * 
     * @var array
     */
    protected $deletedOrdersReport = array();

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
            if (false === Mage::helper('fraisrconnect/synchronisation_order')
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
        $this->outputSynchronisationReport();
    }

    /**
     * Get order collection and loop through every order
     * 
     * @return void
     */
    public function synchronizeOrders()
    {
        $orderSyncHelper = Mage::helper('fraisrconnect/synchronisation_order');
        $config = Mage::getModel('fraisrconnect/config');
        $orderItemsToSynchronize = $orderSyncHelper->getOrderItemsToSynchronize();

        //Loop through every order
        foreach ($orderItemsToSynchronize as $orderItem) {
            try {
                //Validate order/order_item
                if (false === $this->isOrderItemValid($orderItem)) {
                    continue;
                }

                /**
                 * New order
                 *
                 * Only if fraisr_order_id is empty (only the case if the order was not transmitted as a new order)
                 * AND the status is one of the allowed status for new orders
                 */
                if (true === is_null($orderItem->getFraisrOrderId())
                    && true === in_array($orderItem->getData('status'), $config->getOrderExportOrderStatus())) {
                    $this->requestNewOrder($orderItem);
                }

                /**
                 * Update order
                 *
                 * Only if fraisr_order_id exists (only the case if the order was once transmitted as a new order)
                 * AND the transmitted fraisr_qty_ordered is different from the current calculated amount
                 */
                if (false === is_null($orderItem->getFraisrOrderId())
                    && $orderItem->getFraisrQtyOrdered() != $orderSyncHelper->getOrderItemQty($orderItem)) {
                    $this->requestUpdateOrder($orderItem);
                }
            } catch (Fraisr_Connect_Model_Api_Exception $e) {
                $logDetails = $this->prepareOrderRequestData($orderItem);
                $logDetails['fraisr_order_id'] = $orderItem->getFraisrOrderId();
                $logDetails['error_message'] = $e->getMessage();

                //Add item to failed order list
                $this->failedOrdersReport[] = $logDetails;
            }

            //Check if the script runtime is already close to exceed
            if (true === $orderSyncHelper->isRuntimeExceeded($this->synchronisationStartTime)) {
                //Break the loop, stop the syncronisation and return
                return;
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

        //If amount is not greater then 0 -> continue
        if ($orderRequestData['amount'] < 1) {
            return;
        }

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
            'fraisr_order_id' => $orderItem->getFraisrOrderId(),
            'product' => $orderRequestData['product'],
            'amount' => $orderRequestData['amount'],
            'price' => $orderRequestData['price'],
            'donation' => $orderRequestData['donation'],
            'cause' => $orderRequestData['cause']
        );
    }

    /**
     * Trigger update order request and save new fraisr_qty_ordered
     * 
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return void
     */
    protected function requestUpdateOrder($orderItem)
    {
        $amount = Mage::helper('fraisrconnect/synchronisation_order')->getOrderItemQty($orderItem);

        $reponse = Mage::getModel('fraisrconnect/api_request')->requestPut(
            Mage::getModel('fraisrconnect/config')->getOrderApiUri(
                $orderItem->getFraisrOrderId()
            ),
            array('amount' => $amount)
        );

        //Save FraisrQtyOrdered
        $orderItem
            ->setFraisrQtyOrdered($amount)
            ->save();

        if ($amount > 0) {
            //Add order_id to update success list
            $this->updatedOrdersReport[] = array(
                'magento_order_id' => $orderItem->getIncrementId(),
                'fraisr_order_id' => $orderItem->getFraisrOrderId(),
                'amount' => $amount,
            );
        } else {
            //Add order_id to delete success list
            $this->deletedOrdersReport[] = array(
                'magento_order_id' => $orderItem->getIncrementId(),
                'fraisr_order_id' => $orderItem->getFraisrProductId(),
                'amount' => $amount,
            );
        }
    }

    /**
     * Output order synchronisation report
     *
     * Write admin notification messages
     * And generate an overview log entry
     * 
     * @return void
     */
    protected function outputSynchronisationReport()
    {
        //Add admin notice message about new added orders
        $newOrdersMessage = $this->getAdminHelper()->__(
            '%s order(s) were successfully added to fraisr.',
            (int) count($this->newOrdersReport)
        );
        if (count($this->newOrdersReport) > 0) {
            Mage::getSingleton('adminhtml/session')->addNotice($newOrdersMessage);
        }

        //Add admin notice message about updated orders
        $updatedOrdersMessage = $this->getAdminHelper()->__(
            '%s order(s) were successfully updated in fraisr.',
            (int) count($this->updatedOrdersReport)
        );
        if (count($this->updatedOrdersReport) > 0) {
            Mage::getSingleton('adminhtml/session')->addNotice($updatedOrdersMessage);
        }

        //Add admin notice message about deleted orders
        $deletedOrdersMessage = $this->getAdminHelper()->__(
            '%s order(s) were successfully deleted from fraisr.',
            (int) count($this->deletedOrdersReport)
        );
        if (count($this->deletedOrdersReport) > 0) {
            Mage::getSingleton('adminhtml/session')->addNotice($deletedOrdersMessage);
        }

        //Add admin notice message about transmission failed orders
        $failedOrdersMessage = $this->getAdminHelper()->__(
            'The transmission of %s order(s) failed during fraisr synchronisation.',
            (int) count($this->failedOrdersReport)
        );
        if (count($this->failedOrdersReport) > 0) {
            Mage::getSingleton('adminhtml/session')->addNotice($failedOrdersMessage);
        }

        //Write detailed log report
        $logMessage = sprintf(
            "#%s\n%s\n\n"
            ."#%s\n%s\n\n"
            ."#%s\n%s\n\n"
            ."#%s\n%s\n\n",
            $newOrdersMessage,
            Mage::helper('fraisrconnect/synchronisation_order')->buildSyncReportDetails($this->newOrdersReport),
            $updatedOrdersMessage,
            Mage::helper('fraisrconnect/synchronisation_order')->buildSyncReportDetails($this->updatedOrdersReport),
            $deletedOrdersMessage,
            Mage::helper('fraisrconnect/synchronisation_order')->buildSyncReportDetails($this->deletedOrdersReport),
            $failedOrdersMessage,
            Mage::helper('fraisrconnect/synchronisation_order')->buildSyncReportDetails($this->failedOrdersReport)
        );
        Mage::getModel('fraisrconnect/log')
            ->setTitle($this->getAdminHelper()->__('Order synchronisation report'))
            ->setMessage($logMessage)
            ->setTask(Fraisr_Connect_Model_Log::LOG_TASK_ORDER_SYNC)
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
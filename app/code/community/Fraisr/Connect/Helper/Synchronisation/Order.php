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
 * Order Synchronisation Helper
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Helper_Synchronisation_Order extends Fraisr_Connect_Helper_Synchronisation_Abstract
{
    /**
     * Get order item collection which has to be synchronized
     * 
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getOrderItemsToSynchronize()
    {
        //Get config model
        $config = Mage::getModel('fraisrconnect/config');

        //Calculate filter date
        $gmtDate = Mage::getModel('core/date')->gmtDate(
            null,
            Mage::getModel('core/date')->timestamp() - ($config->getOrderExportDays() * 24 * 60 * 60)
        );

        //Get order collection and add filters
        $orderItemCollection = Mage::getModel('sales/order_item')
            ->getCollection()
            ->addFieldToFilter('main_table.fraisr_product_id', array('notnull' => 'true'))
            ->addFieldToFilter('main_table.parent_item_id', array('null' => 'true'))
            ->addFieldToFilter('main_table.updated_at', array('gt' => $gmtDate))
        ;

        //Join sales_order - table => necessary to filter for order_status and to get the increment_id
        $orderItemCollection
            ->getSelect()
            ->join(
                array(
                    'sales_order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
                    'main_table.order_id = sales_order.entity_id',
                    array(
                        'sales_order.increment_id',
                        'sales_order.status',
                        'sales_order.base_currency_code',
                        'sales_order.order_currency_code'
                    )
            );

        return $orderItemCollection;
    }

    /**
     * Check if the order_item is valid to be transferred to fraisr
     *
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return boolean
     */
    public function isOrderItemValid($orderItem)
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

    public function getJsonObject($orderItem){
        $price = 0;
        if ('EUR' === $orderItem->getBaseCurrencyCode()) {
            $price = $orderItem->getBasePriceInclTax();
        } elseif ('EUR' === $orderItem->getOrderCurrencyCode()) {
            $price = $orderItem->getPriceInclTax();
        }

        return array(
            'external_id' => $orderItem->getId(),
            'fraisr_id' => $orderItem->getFraisrOrderId(),
            'product' => $orderItem->getFraisrProductId(),
            'amount' => $this->getOrderItemQty($orderItem),
            'price' => $price,
            'cause' => $orderItem->getFraisrCauseId(),
            'donation' => $orderItem->getFraisrDonationPercentage()
        );
    }

    /**
     * Get order item qty for fraisr-synchronisation
     * 
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return array
     */
    public function getOrderItemQty($orderItem)
    {
        //reference ordered amount - depending on configuration ordered or invoiced items
        if (true === Mage::getModel('fraisrconnect/config')->getOrderExportInvoiceReference()) {
            return (int) ($orderItem->getQtyInvoiced() - $orderItem->getQtyRefunded());
        } else {
            return (int) ($orderItem->getQtyOrdered() - $orderItem->getQtyRefunded() - $orderItem->getQtyCanceled());
        }
    }

    /**
     * Create a new order synchronisation cron task to continue the previous one (+x minutes)
     *
     * @param Mage_Cron_Model_Schedule $observer
     * @return Mage_Cron_Core_Schedule
     */
    public function createOrderSyncCronTask($observer)
    {
        //Check if the $observer consists the necessary data and has the correct jobCode
        if ('fraisrconnect_synchronisation_orders' != $observer->getJobCode()) {
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
}
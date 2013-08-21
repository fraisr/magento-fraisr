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
class Fraisr_Connect_Helper_Synchronisation_Order extends Fraisr_Connect_Helper_Data
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
            ->addFieldToFilter('sales_order.status', array('in' => $config->getOrderExportOrderStatus()))
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
     * Get order item qty for fraisr-synchronisation
     * 
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return array
     */
    public function getOrderItemQty($orderItem)
    {
        return (int) ($orderItem->getQtyOrdered()
            - $orderItem->getQtyRefunded()
            - $orderItem->getQtyCanceled()
        );

    }
}
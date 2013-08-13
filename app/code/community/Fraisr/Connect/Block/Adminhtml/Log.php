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
 * Log Block
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $helper = Mage::helper('fraisrconnect/data');

        $this->_blockGroup = 'fraisrconnect';
        $this->_controller = 'adminhtml_log';
        $this->_headerText = $helper->__('fraisr log');

        $this->changeButtons();
    }

    /**
     * Remove default buttons and add new sync buttons
     * 
     * @return void
     */
    protected function changeButtons()
    {
        $helper = Mage::helper('fraisrconnect/data');
        $urlModel  = Mage::getModel('adminhtml/url');
        
        $this->_removeButton('add');

        //Add cause sync button
        $this->_addButton('cause_synchronisation', array(
            'label'     => $helper->__('Synchronize causes'),
            'onclick'   => 'setLocation(\'' . $urlModel->getUrl('fraisrconnect/adminhtml_synchronisation/cause') .'\')',
            'class'     => 'add',
        ));

        //Add category sync button
        $this->_addButton('category_synchronisation', array(
            'label'     => $helper->__('Synchronize categories'),
            'onclick'   => 'setLocation(\'' . $urlModel->getUrl('fraisrconnect/adminhtml_synchronisation/category') .'\')',
            'class'     => 'add',
        ));

        //Add mark products to sync button
        $this->_addButton('product_mark_to_synchronisation', array(
            'label'     => $helper->__('Mark products as to synchronize'),
            'onclick'   => 'setLocation(\'' . $urlModel->getUrl('fraisrconnect/adminhtml_synchronisation/markProduct') .'\')',
            'class'     => 'add',
        ));

        //Add product sync button
        $this->_addButton('product_synchronisation', array(
            'label'     => $helper->__('Synchronize products'),
            'onclick'   => 'setLocation(\'' . $urlModel->getUrl('fraisrconnect/adminhtml_synchronisation/product') .'\')',
            'class'     => 'add',
        ));
    }
}

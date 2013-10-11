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
 * Synchronisation Controller
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Adminhtml_SynchronisationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if the admin user is allowed to execute this controller action
     * 
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
                ->isAllowed('system/tools/fraisrconnect_synchronisation');
    }

    /**
     * Check if the admin user is allowed to see this section
     *
     * @param string $section
     * @param boolean
     */
    protected function _checkSectionAllowed($section)
    {
        if (false == Mage::getSingleton('admin/session')
                ->isAllowed('system/tools/fraisrconnect_synchronisation/')) {
            $this->forward('denied');
        }
    }

    /**
     * Trigger cause synchronisation
     * 
     * @return void
     */
    public function causeAction()
    {
        if (true === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            //Retrieve and save causes
            Mage::getModel('fraisrconnect/cause')->synchronize();

            /**
             * Check if products exists which causes doesn't exist anymore
             * If some were find, set 'fraisr_enabled' to false
             */
            Mage::getModel('fraisrconnect/cause')->productCheck();
        }

        $this->_redirectReferer();
        return;
    }

    /**
     * Trigger category synchronisation
     * 
     * @return void
     */
    public function categoryAction()
    {
        if (true === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            Mage::getModel('fraisrconnect/category')->synchronize();
        }

        $this->_redirectReferer();
        return;
    }

    /**
     * Trigger product synchronisation
     * 
     * @return void
     */
    public function productAction()
    {
        if (true === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            $productSyncronisation = Mage::getModel('fraisrconnect/product');
            $productSyncronisation->synchronize();

            if (false === $productSyncronisation->isSynchronisationComplete()) {
                Mage::getSingleton('adminhtml/session')->addWarning(
                    Mage::helper('fraisrconnect/data')->__('Not all products have been synchronized because of a transmission error or a script timeout. Please start the process again.')
                );
            }
        }

        $this->_redirectReferer();
        return;
    }

    /**
     * Trigger mark products to synchronisation
     * 
     * @return void
     */
    public function markProductAction()
    {
        if (true === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            Mage::getModel('fraisrconnect/product')->markProductsAsToSynchronize();
        }

        $this->_redirectReferer();
        return;
    }

    /**
     * Trigger order synchronisation
     * 
     * @deprecated
     */
    public function orderAction()
    {
        // if (true === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
        //     $orderSyncronisation = Mage::getModel('fraisrconnect/order');
        //     $orderSyncronisation->synchronize();

        //     if (false === $orderSyncronisation->isSynchronisationComplete()) {
        //         Mage::getSingleton('adminhtml/session')->addWarning(
        //             Mage::helper('fraisrconnect/data')->__('Not all orders have been synchronized because of a transmission error or a script timeout. Please start the process again.')
        //         );
        //     } else {
        //         Mage::getSingleton('adminhtml/session')->addSuccess(
        //             Mage::helper('fraisrconnect/data')->__('fraisr order synchronisation completed.')
        //         );
        //     }
        // }

        $this->_redirectReferer();
        return;
    }
}
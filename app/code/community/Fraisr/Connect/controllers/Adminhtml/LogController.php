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
 * Log Controller
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Adminhtml_LogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Log index
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent(
            $this->getLayout()->createBlock('fraisrconnect/adminhtml_log', 'fraisrconnect_adminhtml_log')
        );
        $this->renderLayout();
    }

    /**
     * Show log detail
     * 
     * @return void
     */
    public function editAction()
    {
        $this->loadLayout();
        $this->_addContent(
            $this->getLayout()->createBlock('fraisrconnect/adminhtml_log_edit', 'fraisrconnect_adminhtml_log_edit')
        );
        $this->renderLayout();
    }
}
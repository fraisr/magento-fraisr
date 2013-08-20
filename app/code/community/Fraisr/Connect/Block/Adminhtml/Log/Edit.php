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
 * Log Edit Block
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Adminhtml_Log_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = "id";
        $this->_blockGroup = 'fraisrconnect';
        $this->_mode = 'edit';
        $this->_controller = 'adminhtml_log';
        
        //Add Log Entry to Session
        if( $this->getRequest()->getParam($this->_objectId) ) {
            $formData = Mage::getModel('fraisrconnect/log')
                ->load($this->getRequest()->getParam($this->_objectId));
                
            Mage::register('current_fraisrlog', $formData);
        }
        
        //Remove reset, save and delete button -> Not necessary because we only want to show the log details (not edit them)
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Get Header Text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('fraisrconnect/data')->__('fraisr log');
    }
}
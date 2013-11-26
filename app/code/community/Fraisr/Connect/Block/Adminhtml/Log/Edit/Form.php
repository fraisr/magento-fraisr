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
 * Log Edit Form
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Adminhtml_Log_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare Form
     *
     * @return object
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('fraisrconnect/data');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post'
        ));
        
        $logData = Mage::registry('current_fraisrlog')->getData();
        
        $fieldset = $form->addFieldset(
            'edit_fraisrlog',
            array('legend' => Mage::helper('fraisrconnect/data')->__('fraisr log'))
        );

        $fieldset->addField('type', 'select', array(
            'name' => 'type',
            'title' => $helper->__('Type'),
            'label' => $helper->__('Type'),
            'maxlength' => '50',
            'style' => 'width:98%;',
            'options' => Mage::getModel('fraisrconnect/log')->getTypeOptions()
        )); 

        $fieldset->addField('task', 'select', array(
            'name' => 'task',
            'title' => $helper->__('Task'),
            'label' => $helper->__('Task'),
            'maxlength' => '50',
            'style' => 'width:98%;',
            'options' => Mage::getModel('fraisrconnect/log')->getTaskOptions()
        ));

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'title' => $helper->__('Title'),
            'label' => $helper->__('Title'),
            'maxlength' => '50',
            'style' => 'width:98%;',
        ));
        
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $fieldset->addField('created_at', 'date', array(
            'name' => 'created_at',
            'title' => $helper->__('Date'),
            'label' => $helper->__('Date'),
            'format' => $outputFormat,
            'maxlength' => '50',
            'style'   => 'width:98%;',
        ));
        
        $fieldset->addField('message', 'textarea', array(
            'name' => 'message',
            'title' => $helper->__('Message'),
            'label' => $helper->__('Message'),
            'style' => 'width: 700px; height: 400px;',
        )); 
        
        $fieldset->addField('additional_information1', 'textarea', array(
            'name' => 'additional_information1',
            'title' => $helper->__('Additional information 1'),
            'label' => $helper->__('Additional information 1'),
            'style' => 'width: 700px; height: 400px;',
        )); 
        
        $fieldset->addField('additional_information2', 'textarea', array(
            'name' => 'additional_information2',
            'title' => $helper->__('Additional information 2'),
            'label' => $helper->__('Additional information 2'),
            'style' => 'width: 700px; height: 400px;',
        ));
        
        $form->setValues(Mage::registry('current_fraisrlog')->getData());
        
        if ($logData['created_at']!="") {
            $form->getElement('created_at')->setValue(
                Mage::app()->getLocale()->date(
                    $logData['created_at'],
                    Varien_Date::DATETIME_INTERNAL_FORMAT
                )
            ); 
        }
        
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
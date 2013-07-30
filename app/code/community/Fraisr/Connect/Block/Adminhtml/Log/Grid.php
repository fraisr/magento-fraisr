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
 * Log Widget Grid
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct
     *
     * @return void
     */  
    public function __construct()
    {
        parent::__construct();
        $this->setId('fraisrconnectLogIndex');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
    }

    /**
     * Prepare Collection
     *
     * @return object
     */ 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fraisrconnect/log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }   

    /**
     * Prepare Columns
     *
     * @return object
     */ 
    protected function _prepareColumns()
    {
        $this->addColumn('type', array(
            'header'    => Mage::helper('fraisrconnect/data')->__('Type'),
            'align'     => 'left',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Mage::getModel('fraisrconnect/log')->getTypeOptions(),
        ));

        $this->addColumn('title', array(
            'header'        => Mage::helper('fraisrconnect/data')->__('Title'),
            'align'         => 'left',
            'filter_index'  => 'title',
            'index'         => 'title',
        ));

        $this->addColumn('task', array(
            'header'         => Mage::helper('fraisrconnect/data')->__('Task'),
            'align'         => 'left',
            'index'         => 'task',
            'filter_index'  => 'task',
            'type'      => 'options',
            'options'   => Mage::getModel('fraisrconnect/log')->getTaskOptions(),
        ));
        
        $this->addColumn('created_at', array(
            'header'        => Mage::helper('fraisrconnect/data')->__('Date'),
            'align'         => 'left',
            'filter_index'  => 'created_at',
            'index'         => 'created_at',
            'type'          => 'datetime'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
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
 * Cause Source Model for cause product attribute
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Entity_Attribute_Source_Cause extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    /**
     * @const FRAISR_CAUSE_DEFAULT Default case value
     */
    const FRAISR_CAUSE_DEFAULT = '';

    /**
     * Retrieve all causes
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('adminhtml/data')->__('-- Please Select --'),
                    'value' => '',
                )
            );

            $causeCollection = Mage::getModel('fraisrconnect/cause')->getCollection();
            //If no causes exist, add a notice that causes have to be synched
            if (true === Mage::getModel('fraisrconnect/config')->isActive()
                && $causeCollection->count() === 0) {
                Mage::getSingleton('adminhtml/session')->addNotice(
                    Mage::helper('fraisrconnect/data')->__('fraisr causes have to be synchronized.')
                );
            }

            //For every synched cause => create a select option
            foreach ($causeCollection as $cause) {
                //Add restriction in brackets if existing
                $restriction = '';
                if (false === is_null($cause->getRestrictions())) {
                    $restriction = ' ('.$cause->getRestrictions().')';
                }

                $this->_options[] = array(
                    'label' => $cause->getName().$restriction,
                    'value' =>  $cause->getId(),
                );
            }
        }
        return $this->_options;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        return array($this->getAttribute()->getAttributeCode() => array(
            'type'      => 'varchar(255)',
            'unsigned'  => false,
            'is_null'   => true,
            'default'   => null,
            'extra'     => null
        ));
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceSingleton('eav/entity_attribute')->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
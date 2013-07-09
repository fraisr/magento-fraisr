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
class Fraisr_Connect_Model_Entity_Attribute_Source_Cause
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
   /**
     * @const FRAISR_CAUSE_DEFAULT Default case value
     */
    const FRAISR_CAUSE_DEFAULT = "";

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
                    'label' => Mage::helper('adminhtml/data')->__("-- Please Select --"),
                    'value' => "",
                )
            );

            //For every synched cause => create a select option
            foreach (Mage::getModel("fraisrconnect/cause")->getCollection() as $cause) {
                $this->_options[] = array(
                    'label' => $cause->getName(),
                    'value' =>  $cause->getId(),
                );
            }
        }
        return $this->_options;
    }
}
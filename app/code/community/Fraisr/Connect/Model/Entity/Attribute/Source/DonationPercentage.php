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
 * DonationPercentage Source Model for donation product attribute
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Entity_Attribute_Source_DonationPercentage
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Allowed donation percentages
     * 
     * @var array 
     */
    protected $donationPercentages = array(
        5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100
    );

   /**
     * @const FRAISR_DONATION_PERCENTAGE_DEFAULT Default donation value
     */
    const FRAISR_DONATION_PERCENTAGE_DEFAULT = "";

    /**
     * Retrieve all donation percentages
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

            //For every defined percentage => create a select option
            foreach ($this->donationPercentages as $percentage) {
                $this->_options[] = array(
                    'label' => Mage::helper('fraisrconnect/data')->__("%s %s", $percentage, "%"),
                    'value' =>  $percentage,
                );
            }
        }

        return $this->_options;
    }
}
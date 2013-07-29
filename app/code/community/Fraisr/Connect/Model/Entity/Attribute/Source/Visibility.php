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
 * Visibility Source Model for visibility product attribute
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Entity_Attribute_Source_Visibility
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
   /**
     * @const FRAISR_VISIBILITY_KEY Translation prefix constant
     */
    const FRAISR_VISIBILITY_KEY = "visibility_";

   /**
     * @const FRAISR_VISIBILITY_BOTH Visible in fraisr and in the shop
     */
    const FRAISR_VISIBILITY_BOTH = "both";

   /**
     * @const FRAISR_VISIBILITY_SHOP Visible in the shop only
     */
    const FRAISR_VISIBILITY_SHOP = "shop";

   /**
     * @const FRAISR_VISIBILITY_FRAISR Visible in fraisr only
     */
    const FRAISR_VISIBILITY_FRAISR = "fraisr";

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
                    'label' => Mage::helper('fraisrconnect/data')->__(
                        self::FRAISR_VISIBILITY_KEY.self::FRAISR_VISIBILITY_BOTH
                    ),
                    'value' => self::FRAISR_VISIBILITY_BOTH,
                ),
                array(
                    'label' => Mage::helper('fraisrconnect/data')->__(
                        self::FRAISR_VISIBILITY_KEY.self::FRAISR_VISIBILITY_SHOP
                    ),
                    'value' => self::FRAISR_VISIBILITY_SHOP,
                ),
                array(
                    'label' => Mage::helper('fraisrconnect/data')->__(
                        self::FRAISR_VISIBILITY_KEY.self::FRAISR_VISIBILITY_FRAISR
                    ),
                    'value' => self::FRAISR_VISIBILITY_FRAISR,
                ),
            );
        }
        return $this->_options;
    }
}
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
 * Category Source Model for category product attribute
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Entity_Attribute_Source_Category
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
   /**
     * @const FRAISR_CATEGORY_DEFAULT Default category value
     */
    const FRAISR_CATEGORY_DEFAULT = "";

    /**
     * Retrieve all categories
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

            //For every synched category => create a select option
            foreach (Mage::getModel("fraisrconnect/category")->getCollection() as $category) {
                if (true === is_null($category->getParentId())) {
                    $this->_options[$category->getId()] = array(
                        'label' => $category->getName(),
                        'value' =>  array(),
                    );
                } else {
                    $this->_options[$category->getParentId()]["value"][] = array(
                        'label' => $category->getName(),
                        'value' =>  $category->getId(),
                    );
                }
            }
        }
        return $this->_options;
    }
}
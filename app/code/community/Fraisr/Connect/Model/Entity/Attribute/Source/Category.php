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
    const FRAISR_CATEGORY_DEFAULT = '';

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
                    'label' => Mage::helper('adminhtml/data')->__('-- Please Select --'),
                    'value' => '',
                )
            );

            $categoryCollection = Mage::getModel('fraisrconnect/category')->getCollection();

            //If no categories exist, add a notice that categories have to be synched
            if (true === Mage::getModel('fraisrconnect/config')->isActive()
                && $categoryCollection->count() === 0) {
                Mage::getSingleton('adminhtml/session')->addNotice(
                    Mage::helper('fraisrconnect/data')->__('fraisr categories have to be synchronized.')
                );
            }

            $isParent = array();
            $parent = array();
            foreach ($categoryCollection as $category){
                $parent[$category->getId()] = $category->getParentId();
                if (true === is_null($category->getParentId())) {
                    $isParent[$category->getId()] = true;
                } else {
                    $isParent[$category->getParentId()] = true;
                }
            }

            //For every synched category => create a select option
            foreach ($categoryCollection as $category) {
                //Parent product -> Just create an optgroup option
                if (true === is_null($category->getParentId())) {
                    $this->_options[$category->getId()] = array(
                        'label' => $category->getName(),
                        'value' =>  array(),
                    );
                } elseif (array_key_exists($category->getId(), $isParent)) {
                    $label = $category->getName();
                    $parents = array();
                    $p = $category->getId();
                    $o = &$this->_options;
                    $i = 0;

                    while(($p = $parent[$p]) !== null){
                        array_unshift($parents, $p);
                        $i++;
                    }

                    foreach($parents AS $c => $p){
                        if($c + 1 == $i){
                            $label = $o[$p]["label"] . " / " . $label;
                        }
                        $o = &$o[$p]["value"];
                    }

                    $o[$category->getId()] = array(
                        'label' => $label,
                        'value' =>  array(),
                    );
                } else {
                    $label = $category->getName();

                    //Add label in brackets if existing
                    if (false === is_null($category->getLabel())) {
                        $label .= ' ('.$category->getLabel().')';
                    }

                    $parents = array();
                    $p = $category->getId();
                    $o = &$this->_options;
                    $i = 0;

                    while(($p = $parent[$p]) !== null){
                        array_unshift($parents, $p);
                        $i++;
                    }

                    foreach($parents AS $c => $p){
                        if($c + 1 == $i){
                            $label = $o[$p]["label"] . " / " . $label;
                        }
                        $o = &$o[$p]["value"];
                    }

                    $o[] = array(
                        'label' => $label,
                        'value' =>  $category->getId(),
                    );
                }
            }
        }
        return $this->_options;
    }
}
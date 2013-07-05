
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
 * Product Attributes Source Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_System_Config_Source_ProductAttribute
{
    /**
     * Get product attribures options for extension configuration
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $productAttributesCollection = $this->getProductAttributesCollection();
    
        $productAttributes = array();
        foreach($productAttributesCollection->getItems() as $productAttribute) {
            $productAttributes[] = array(
                'value' => $productAttribute->getAttributeCode(),
               'label' => $productAttribute->getFrontendLabel()
            );
        }
        return $productAttributes;
    }

    /**
     * Retrieve collection of all product attribures
     * 
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function getProductAttributesCollection() {
        $collection = Mage::getResourceModel( 'catalog/product_attribute_collection' );
        $collection->addFilter( "is_visible", 1 );
        return $collection;
    }
}

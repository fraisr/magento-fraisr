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
 * Catalog Product List Json Block
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Catalog_Product_Json_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Get product list json
     * 
     * @return string
     */
    public function getProductListJson()
    {
        $fraisrProductData = array();

        //Get every product of the current selection and loop through it
        foreach ($this->getLoadedProductCollection() as $product) {
            //Continue only if product is tagged with 'Fraisr':'Yes' and has a 'fraisr_id'
            if ('1' !== $product->getFraisrEnabled()
                || true === is_null($product->getFraisrId())) { 
                continue;
            }

            //Add product entry
            $fraisrProductData[$product->getProductUrl()] = Mage::helper('fraisrconnect/data')
                ->buildProductJsonEntry($product);
        }
        
        return Zend_Json::encode($fraisrProductData);
    }
}
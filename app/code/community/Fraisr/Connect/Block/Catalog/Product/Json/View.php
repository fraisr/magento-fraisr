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
 * Catalog Product View Json Block
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Catalog_Product_Json_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Get product detail json
     * 
     * @return string
     */
    public function getProductDetailJson()
    {
        $fraisrProductData = array();
        $product = $this->getProduct();

        //Continue only if product is tagged with 'Fraisr':'Yes' and has a 'fraisr_id'
        /**
         * TODO: Uncomment later if products have a fraisr_id
         * || true === is_null($product->getFraisrId()
         */
        if (!($product instanceof Mage_Catalog_Model_Product)
            || true === is_null($product->getId())
            || '1' !== $product->getFraisrEnabled()) { 
            continue;
        }

        //Add product entry
        $fraisrProductData[] = Mage::helper('fraisrconnect/data')
                ->buildProductJsonEntry($product);
        
        return Zend_Json::encode($fraisrProductData);
    }
}
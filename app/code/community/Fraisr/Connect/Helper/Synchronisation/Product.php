
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
 * Product Synchronisation Helper
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Helper_Synchronisation_Product extends Mage_Core_Helper_Abstract
{
    /**
    * Calculate price and special price information
    * 
    * @param Mage_Catalog_Model_Product $product
    * @return array
    */
    public function calculatePrices($product)
    {
        $prices = array();

        //active special price
        if ($product->getPrice() > $product->getFinalPrice()) {
            $prices["special_price"] = round($product->getFinalPrice(), 2);
            $prices["price"] = round($product->getPrice(), 2);
        } else { //normal price
            $prices["special_price"] = "";
            $prices["price"] = round($product->getFinalPrice(), 2);
        }
        return $prices;
    }
}

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
 * Main Helper
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Build product JSON entry
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function buildProductJsonEntry($product)
    {
        return array(
            'cause' => $product->getFraisrCause(),
            'donation' => $product->getFraisrDonationPercentage(),
            'category' => $product->getFraisrCategory(),
            'internalid' => $product->getSku(),
            'name' => $product->getName(),
        );
    }
}

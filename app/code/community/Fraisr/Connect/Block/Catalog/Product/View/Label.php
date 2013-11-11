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
 * Catalog Product View Label
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Catalog_Product_View_Label extends Mage_Catalog_Block_Product_View
{
    /**
     * Get fraisr donation label iframe
     * 
     * @return string
     */
    public function getIframeUrl()
    {
        $config = Mage::getModel('fraisrconnect/config');

        //Get base64 encoded hash
        $base64Hash = Mage::helper('fraisrconnect/data')->buildIframeHash(
            $this->getProduct()->getFraisrId()
        );

        //Return iframe url
        return $config->getTrustedUri().$config->getDonationLabelIframeUri(
            substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2),
            $base64Hash
        );
    }

    /**
     * Check if product is active in fraisr
     * 
     * @return boolean
     */
    public function productIsActiveInFraisr()
    {
        if (false === is_null($this->getProduct()->getFraisrId())
            && 1 == $this->getProduct()->getFraisrEnabled()) {
            return true;
        }
        return false;
    }
}
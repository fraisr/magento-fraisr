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
 * Frontend Widget Block
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Frontend_Widget extends Mage_Core_Block_Template
{
    /**
     * Get fraisr widget js uri
     * 
     * @return string
     */
    public function getFraisrWidgetJsUri()
    {
        return Mage::getModel('fraisrconnect/config')->getFrontendWidgetJsUri();
    }

    /**
     * Get fraisr widget css uri
     * 
     * @return string
     */
    public function getFraisrWidgetCssUri()
    {
        return Mage::getModel('fraisrconnect/config')->getFrontendWidgetCssUri();
    }
}
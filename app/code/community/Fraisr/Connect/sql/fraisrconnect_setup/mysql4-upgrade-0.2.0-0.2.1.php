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

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$fraisrHelper = Mage::helper("fraisrconnect/data");

/**
 * Update product product list visibility
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_enabled',
    'used_in_product_listing',
    true
);
$setup->updateAttribute(
    'catalog_product',
    'fraisr_cause',
    'used_in_product_listing',
    true
);
$setup->updateAttribute(
    'catalog_product',
    'fraisr_donation_percentage',
    'used_in_product_listing',
    true
);
$setup->updateAttribute(
    'catalog_product',
    'fraisr_category',
    'used_in_product_listing',
    true
);
$setup->updateAttribute(
    'catalog_product',
    'fraisr_id',
    'used_in_product_listing',
    true
);

$installer->endSetup();

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
 * Setup values
 */
$fraisrAttributeGroup = "Fraisr";
$productTypes = array('simple');

/**
 * Change attribute type to varchar because Fraisr IDs consist alphabetic chars too
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_category',
    'backend_type',
    'varchar'
);
$setup->updateAttribute(
    'catalog_product',
    'fraisr_cause',
    'backend_type',
    'varchar'
);

/**
 * Set frontend input renderer for fraisr ID to set this field as readonly in the backend
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_id',
    'frontend_input_renderer',
    'fraisrconnect/adminhtml_entity_attribute_fraisrId'
);

$installer->endSetup();

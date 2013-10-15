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
 * @copyright  Copyright (c) 2013 fraisr GmbH <hello@fraisr.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Christopher Knötschke <chris@fraisr.com>
 */

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$fraisrHelper = Mage::helper("fraisrconnect/data");

/**
 * Setup values
 */
$fraisrAttributeGroup = "fraisr";
$productTypes = array(
    'bundle',
    'virtual',
    'simple',
    'configurable',
    'downloadable'
);

//fraisr sync hash
$setup->addAttribute(
    'catalog_product',
    'fraisr_sync_hash',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'text',
        'type'                          => 'varchar',
        'default'                       => null,
        'label'                         => $fraisrHelper->__("fraisr Sync Hash"),
        'note'                          => $fraisrHelper->__("Wird bei der Produkt-Synchronisation vergeben."),
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => implode(",", $productTypes),
        'sort_order'                    => 500,
        'visible'                       => 0,
        'required'                      => 0,
        'user_defined'                  => 1,
        'searchable'                    => 0,
        'filterable'                    => 0,
        'comparable'                    => 1,
        'visible_on_front'              => 0,
        'visible_in_advanced_search'    => 0,
        'is_html_allowed_on_front'      => 0,
        'used_in_product_listing'       => 1,
    )
);

$installer->endSetup();

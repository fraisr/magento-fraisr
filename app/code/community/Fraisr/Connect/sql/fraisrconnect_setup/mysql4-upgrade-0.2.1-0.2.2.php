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
$fraisrAttributeGroup = "fraisr";
$productTypes = array(
    'bundle',
    'virtual',
    'simple',
    'configurable',
    'downloadable'
); //All types except "grouped"

//Fraisr update flag
$setup->addAttribute(
    'catalog_product',
    'fraisr_update',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'text',
        'type'                          => 'int',
        'default'                       => 0,
        'label'                         => $fraisrHelper->__('fraisr synchronisation iterations'),
                                           //See explanation about german note in setup of attribute "fraisr_visibility"
        'note'                          => $fraisrHelper->__('Wird intern zur Artikel-Synchronisation verwendet.'),
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => implode(",", $productTypes),
        'sort_order'                    => 600,
        'visible'                       => 1,
        'required'                      => 0,
        'user_defined'                  => 1,
        'searchable'                    => 1,
        'filterable'                    => 0,
        'comparable'                    => 1,
        'visible_on_front'              => 1,
        'visible_in_advanced_search'    => 0,
        'is_html_allowed_on_front'      => 0,
        'used_in_product_listing'       => 1,
    )
);

/**
 * Set frontend input renderer for 'fraisr_update' to set this field as readonly in the backend
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_update',
    'frontend_input_renderer',
    'fraisrconnect/adminhtml_entity_attribute_fraisrUpdate'
);

/**
 * Update product type
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_update',
    'apply_to',
    implode(",", $productTypes)
);

/**
 * Update product list visibility
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_update',
    'used_in_product_listing',
    true
);

$installer->endSetup();
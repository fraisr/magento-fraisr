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
 * Add attribute group "Fraisr" too all attribute sets
 */
$setup->addAttributeGroup(
    'catalog_product',
    'Default',
    $fraisrAttributeGroup,
    1000
);

/**
 * Add Fraisr-Attributes to "Fraisr" attribute group
 */

//Is Fraisr article -> Yes/No
$setup->addAttribute(
    'catalog_product',
    'fraisr_enabled',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'select',
        'type'                          => 'int',
        'label'                         => $fraisrHelper->__("Fraisr enabled"),
        'source'                        => 'eav/entity_attribute_source_boolean',
        'default'                       => 0,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => $productTypes,
        'sort_order'                    => 100,
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

//Cause
$setup->addAttribute(
    'catalog_product',
    'fraisr_cause',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'select',
        'type'                          => 'int',
        'label'                         => $fraisrHelper->__("Fraisr cause"),
        'source'                        => 'fraisrconnect/entity_attribute_source_cause',
        'default'                       => Fraisr_Connect_Model_Entity_Attribute_Source_Cause::FRAISR_CAUSE_DEFAULT,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => $productTypes,
        'sort_order'                    => 200,
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

//Donation percentage
$setup->addAttribute(
    'catalog_product',
    'fraisr_donation_percentage',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'select',
        'type'                          => 'int',
        'label'                         => $fraisrHelper->__("Fraisr donation percentage"),
        'source'                        => 'fraisrconnect/entity_attribute_source_donationPercentage',
        'default'                       => Fraisr_Connect_Model_Entity_Attribute_Source_DonationPercentage::FRAISR_DONATION_PERCENTAGE_DEFAULT,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => $productTypes,
        'sort_order'                    => 300,
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

//Category
$setup->addAttribute(
    'catalog_product',
    'fraisr_category',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'select',
        'type'                          => 'int',
        'label'                         => $fraisrHelper->__("Fraisr category"),
        'source'                        => 'fraisrconnect/entity_attribute_source_category',
        'default'                       => Fraisr_Connect_Model_Entity_Attribute_Source_Category::FRAISR_CATEGORY_DEFAULT,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => $productTypes,
        'sort_order'                    => 400,
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

//exit("manual setup end");

$installer->endSetup();

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
$productTypes = array(
    'bundle',
    'virtual',
    'simple',
    'configurable',
    'downloadable'
); //All types except "grouped"

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
        'apply_to'                      => implode(",", $productTypes),
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

//Is Fraisr article -> Yes/No
$setup->addAttribute(
    'catalog_product',
    'fraisr_visibility',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'select',
        'type'                          => 'int',
        'label'                         => $fraisrHelper->__("Fraisr visibility"),
                                           /**
                                            * There seems to be a BUG in Magento that the attribute note cannot be translated.
                                            * As this extension is done for the german market, directly take the german description.
                                            *
                                            * English one is:
                                            * The Fraisr-extension doesn't influence the visibility of the products in the shop frontend. This can be done for example by selecting 'Visibility':'Not Visible Individually'.
                                            */
        'note'                          => $fraisrHelper->__("Die Fraisr-Extension beeinflusst nicht die Sichtbarkeit der Produkte im Frontend. Diese kann zum Bespiel mit der Einstellung 'Sichtbarkeit':'Alleine nicht sichtbar' vorgenommen werden."),
        'source'                        => 'fraisrconnect/entity_attribute_source_visibility',
        'default'                       => Fraisr_Connect_Model_Entity_Attribute_Source_Visibility::FRAISR_VISIBILITY_BOTH,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => implode(",", $productTypes),
        'sort_order'                    => 150,
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
        'type'                          => 'varchar',
        'label'                         => $fraisrHelper->__("Fraisr cause"),
        'source'                        => 'fraisrconnect/entity_attribute_source_cause',
        'default'                       => Fraisr_Connect_Model_Entity_Attribute_Source_Cause::FRAISR_CAUSE_DEFAULT,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => implode(",", $productTypes),
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
        'apply_to'                      => implode(",", $productTypes),
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
        'type'                          => 'varchar',
        'label'                         => $fraisrHelper->__("Fraisr category"),
        'source'                        => 'fraisrconnect/entity_attribute_source_category',
        'default'                       => Fraisr_Connect_Model_Entity_Attribute_Source_Category::FRAISR_CATEGORY_DEFAULT,
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => implode(",", $productTypes),
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

//Interne Fraisr ID
$setup->addAttribute(
    'catalog_product',
    'fraisr_id',
    array(
        'group'                         => $fraisrAttributeGroup,
        'input'                         => 'text',
        'type'                          => 'varchar',
        'label'                         => $fraisrHelper->__("Fraisr ID"),
                                           //See explanation about german note in setup of attribute "fraisr_visibility"
        'note'                          => $fraisrHelper->__("Wird bei der Produkt-Synchronisation vergeben."),
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                      => implode(",", $productTypes),
        'sort_order'                    => 500,
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

$installer->endSetup();

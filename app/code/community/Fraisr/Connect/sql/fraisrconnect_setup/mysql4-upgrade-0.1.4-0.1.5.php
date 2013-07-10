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
 * Change Fraisr Visibility Note
 */
$setup->updateAttribute(
    'catalog_product',
    'fraisr_visibility',
    'note',
    $fraisrHelper->__("Die Fraisr-Extension beeinflusst nicht die Sichtbarkeit der Produkte im Frontend. Diese kann zum Bespiel mit der Einstellung 'Sichtbarkeit':'Alleine nicht sichtbar' vorgenommen werden.")
);

$installer->endSetup();

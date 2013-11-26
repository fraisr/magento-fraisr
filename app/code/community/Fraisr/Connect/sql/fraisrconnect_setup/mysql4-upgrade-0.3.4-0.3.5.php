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
 * Update cause description - add restriction legend
 */

$setup->updateAttribute(
    'catalog_product',
    'fraisr_cause',
    'note',
    $fraisrHelper->__('Folgende Artikel NICHT für die Organisation verkaufen:<br />' . 
        '"ALK": "Alkohol"<br />"TBK": "Tabakwaren"<br />"LDR": "Lederwaren"<br />
"PELZ": "Pelzwaren"<br />"FLS": "Fleischprodukte"<br />"TP": "tierische Produkte"')
);

$installer->endSetup();

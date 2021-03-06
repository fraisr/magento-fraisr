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
$installer->startSetup();


$installer->run("
ALTER TABLE {$this->getTable('sales/quote_item')} ADD `fraisr_cause_id` VARCHAR(50) default NULL;
ALTER TABLE {$this->getTable('sales/quote_item')} ADD `fraisr_donation_percentage` INT(3) default NULL;

ALTER TABLE {$this->getTable('sales/order_item')} ADD `fraisr_cause_id` VARCHAR(50) default NULL;
ALTER TABLE {$this->getTable('sales/order_item')} ADD `fraisr_donation_percentage` INT(3) default NULL;
");

$installer->endSetup();

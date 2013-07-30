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
  DROP TABLE IF EXISTS {$this->getTable('fraisrconnect/log')};

  CREATE TABLE {$this->getTable('fraisrconnect/log')} (
      `id` INT(11) unsigned NOT NULL auto_increment,
      `type` VARCHAR(20) NOT NULL,
      `task` VARCHAR(50) NOT NULL,
      `title` varchar(255) NOT NULL,
      `message` TEXT,
      `additional_information1` TEXT,
      `additional_information2` TEXT,
      `created_at` timestamp default CURRENT_TIMESTAMP,
      PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

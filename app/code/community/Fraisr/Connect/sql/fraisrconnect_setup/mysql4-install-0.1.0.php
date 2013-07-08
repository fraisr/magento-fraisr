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
    DROP TABLE IF EXISTS {$this->getTable('fraisrconnect_cause')};
    CREATE TABLE {$this->getTable('fraisrconnect_cause')} (
        `id` varchar(50) NOT NULL,
        `description` TEXT,
        `name` varchar(255),
        `url` varchar(255), 
        `image_url` varchar(255),
        `official` smallint(1),
        `created_at` timestamp default CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

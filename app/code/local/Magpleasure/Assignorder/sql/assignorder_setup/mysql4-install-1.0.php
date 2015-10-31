<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Assignorder
 * @version    master
 * @copyright  Copyright (c) 2012 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

$installer = $this;

$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mp_assignorder_history_details')};
DROP TABLE IF EXISTS {$this->getTable('mp_assignorder_history')};

CREATE TABLE IF NOT EXISTS {$this->getTable('mp_assignorder_history')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `assign_time` timestamp NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `is_rollback` smallint(1) unsigned NOT NULL default 0,
  PRIMARY KEY (`history_id`),
  KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('mp_assignorder_history_details')} (
  `detail_id` int(15) unsigned NOT NULL auto_increment,
  `history_id` int(11) unsigned NOT NULL,
  `data_key` varchar(255) NOT NULL,
  `from` varchar(255) NULL,
  `to` varchar(255) NULL,
  PRIMARY KEY (`detail_id`),
  KEY `FK_MP_AORDER_DETAIL_HISTORY` (`history_id`),
  CONSTRAINT `FK_MP_AORDER_DETAIL_HISTORY` FOREIGN KEY (`history_id`) REFERENCES `{$this->getTable('mp_assignorder_history')}` (`history_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
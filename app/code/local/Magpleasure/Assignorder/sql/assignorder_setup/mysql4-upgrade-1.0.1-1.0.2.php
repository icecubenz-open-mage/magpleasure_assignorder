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

try {

    /** @var Magpleasure_Assignorder_Model_Mysql4_History_Collection $history */
    $history = Mage::getModel('assignorder/history')->getCollection();
    $history->addFieldToFilter('is_rollback', 1);
    $history->flushSelected();

    $installer->run("

    ALTER TABLE `{$this->getTable('mp_assignorder_history')}`
      DROP COLUMN `is_rollback`,
      ADD COLUMN `is_notified` SMALLINT(1) UNSIGNED DEFAULT 0  NOT NULL AFTER `order_id`;

    ");

} catch (Exception $e) {
  Mage::logException($e);
}

$installer->endSetup();

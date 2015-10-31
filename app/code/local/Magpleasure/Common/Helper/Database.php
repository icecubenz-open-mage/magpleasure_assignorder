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
 * @package    Magpleasure_Common
 * @version    master
 * @copyright  Copyright (c) 2012-2015 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Common_Helper_Database extends Mage_Core_Helper_Abstract
{
    /**
     * @var
     */
    protected $_tableNames;

    /**
     * @var array
     */
    protected $_tableNameFixes = array(
        'customer/customer' => 'customer/entity',
        'customer/group'    => 'customer/customer_group',
    );

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Get Wrapped Table Name
     *
     * @param string $tableName
     *
     * @return string
     */
    public function getTableName($tableName)
    {
        if (!isset($this->_tableNames[$tableName])) {
            /** @var $resource Mage_Core_Model_Resource */
            $resource = Mage::getSingleton('core/resource');
            $tableName = str_replace(array_keys($this->_tableNameFixes), array_values($this->_tableNameFixes), $tableName);
            $this->_tableNames[$tableName] = $resource->getTableName($tableName);
        }

        return $this->_tableNames[$tableName];
    }

    /**
     * Write Connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function getWriteConnection()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_write');
    }

    /**
     * Read Connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function getReadConnection()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_read');
    }

    /**
     * @param      $tableName
     * @param bool $makePlain
     *
     * @return array
     */
    public function getFields($tableName, $makePlain = false)
    {
        $read = $this->getReadConnection();

        $result = $read->fetchAll("SHOW COLUMNS FROM `{$tableName}`;");
        $result = $this->_commonHelper()->getArrays()->rowsKeysStrToLower($result);

        if ($makePlain) {
            $plainResult = array();
            foreach ($result as $row) {
                $plainResult[] = $row['field'];
            }

            return $plainResult;
        } else {
            return $result;
        }
    }

    /**
     * @param $table
     * @param $data
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function insertIntoTable($table, $data)
    {
        $table = Mage::getSingleton('core/resource')->getTableName($table);
        $write = $this->getWriteConnection();

        $write->beginTransaction();
        $write->insert($table, $data);
        $write->commit();
    }

    /**
     * Retrieves data if found
     *
     * @param string $table
     * @param string $fieldName
     * @param mixed  $fieldValue
     *
     * @return array
     */
    public function selectRowFromTable($table, $fieldName, $fieldValue)
    {
        return $this->selectMultiRowFromTable($table, array($fieldName => $fieldValue));
    }

    /**
     * @param       $table
     * @param array $filters
     *
     * @return mixed
     */
    public function selectMultiRowFromTable($table, array $filters)
    {
        $readConnection = $this->getReadConnection();

        $select = new Zend_Db_Select($readConnection);
        $select->from($table);

        foreach ($filters as $field => $fieldValue) {
            $select->where("{$field} = ?", $fieldValue);
        }

        return $readConnection->fetchRow($select);
    }

    /**
     * @param       $table
     * @param       $fieldName
     * @param       $fieldValue
     * @param array $update
     */
    public function updateRowInTable($table, $fieldName, $fieldValue, array $update)
    {
        $this->updateMultiRowInTable($table, array($fieldName => $fieldValue), $update);
    }

    /**
     * @param       $table
     * @param array $filters
     * @param array $update
     *
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function updateMultiRowInTable($table, array $filters, array $update)
    {
        if (empty($update) || empty($filters)) {
            Mage::throwException($this->_commonHelper()->__("Update data isn't correct."));
        }

        $where = array();
        foreach ($filters as $fieldName => $fieldValue) {
            $where[] = "{$fieldName} = '{$fieldValue}'";
        }
        $where = "(" . implode(") AND (", $where) . ")";

        $write = $this->getWriteConnection();
        $write->beginTransaction();
        $write->update($table, $update, "({$where})");
        $write->commit();

        return $this;
    }

    /**
     * @param $tableName
     *
     * @return bool
     */
    public function isTableExists($tableName)
    {
        $read = $this->getReadConnection();
        $tableExistsSql = $read->quoteInto("SHOW TABLE STATUS LIKE ?", $tableName);

        if ($read->fetchRow($tableExistsSql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $tableName
     *
     * @return $this
     */
    public function dropTable($tableName)
    {
        $write = $this->getWriteConnection();

        $table = $write->quoteIdentifier($tableName);
        $query = 'DROP TABLE IF EXISTS ' . $table;
        $write->query($query);

        return $this;
    }

    /**
     * Fetching all via Zend_Db_Expr method.
     * Returns empty array if something wrong with query.
     *
     * @param string      $select
     * @param string|null $from
     * @param string      $where
     * @param string      $etc
     *
     * @return array
     */
    public function fetchAll($select, $from = null, $where = '', $etc = '')
    {
        $result = array();
        $query = $from ? sprintf(
            "SELECT %s FROM %s %s %s",
            $select, $from, $where, $etc
        ) : $select;

        try {
            $sql = new Zend_Db_Expr($query);
            $result = $this->getReadConnection()->fetchAll($sql);
            unset($sql);
        } catch (Zend_Db_Exception $e) {
            $this->_commonHelper()->getException()->logException($e);
        }

        return $result;
    }
}
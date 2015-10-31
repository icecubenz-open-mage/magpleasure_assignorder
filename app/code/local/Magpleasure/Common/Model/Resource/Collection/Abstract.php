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

/**
 * Abstract Resource Collection
 */
class Magpleasure_Common_Model_Resource_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_orWhere = array();

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    public function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Try to get mapped field name for filter to collection
     *
     * @param string
     * @return string
     */
    protected function _getMappedField($field)
    {
        $mappedFiled = parent::_getMappedField($field);
        if ($mappedFiled == $field){
            $mappedFiled = "main_table.".$mappedFiled;
        }
        return $mappedFiled;
    }

    /**
     * Add Common Product Name Filter by Product Id
     *
     * @param $value
     * @return Magpleasure_Common_Model_Resource_Collection_Abstract
     */
    public function addCommonProductName($value)
    {
        $productTable = $this->_commonHelper()->getDatabase()->getTableName("catalog_product_entity");
        $productEntityValue = $this->_commonHelper()->getDatabase()->getTableName("catalog_product_entity_varchar");
        $eavEntityTypeTable = $this->_commonHelper()->getDatabase()->getTableName("eav_entity_type");
        $eavEntityAttribute = $this->_commonHelper()->getDatabase()->getTableName("eav_attribute");

        $entityType = 'catalog_product';
        $attributeCode = 'name';

        $storeId = '0';

        $this->getSelect()
            ->joinInner(array('comProduct'=>$productTable), "main_table.product_id = comProduct.entity_id", array())
            ->joinInner(array('comProductEavEntType'=>$eavEntityTypeTable), "comProductEavEntType.entity_type_code = '{$entityType}'", array())
            ->joinInner(array('comProductEavAttr'=>$eavEntityAttribute), "comProductEavEntType.entity_type_id = comProductEavAttr.entity_type_id AND comProductEavAttr.attribute_code = '{$attributeCode}'", array())
            ->joinInner(array('comProductEavValue'=>$productEntityValue), "comProduct.entity_id = comProductEavValue.entity_id AND comProductEavValue.attribute_id = comProductEavAttr.attribute_id AND comProductEavValue.store_id = '{$storeId}'", array())
            ->where("comProductEavValue.value LIKE '%{$value}%'")
            ;

        return $this;
    }

    /**
     * Add Common Product Name Filter by Order Id
     *
     * @param $value
     * @return Magpleasure_Common_Model_Resource_Collection_Abstract
     */
    public function addCommonSalesOrderId($value)
    {
        $salesOrderTable = $this->_commonHelper()->getDatabase()->getTableName("sales_flat_order");

        $this->getSelect()
            ->joinInner(array('salesOrder'=>$salesOrderTable), "main_table.order_id = salesOrder.entity_id", array())
            ->where("salesOrder.increment_id LIKE '%{$value}%'")
        ;

        return $this;
    }


    /**
     * Add Common Cms Block Filter
     *
     * @param $value
     * @return Magpleasure_Common_Model_Resource_Collection_Abstract
     */
    public function addCommonCmsBlockId($value)
    {
        $cmsBlockTable = $this->_commonHelper()->getDatabase()->getTableName("cms/block");

        $this->getSelect()
            ->joinInner(array('cmsBlock'=>$cmsBlockTable), "main_table.block_id = cmsBlock.block_id", array())
            ->where("cmsBlock.title LIKE '%{$value}%'")
        ;

        return $this;
    }

    protected function _addEntityValueToFilter($entityType, $mainTable, $type, $storeId = null, $attributeCode, $value, $linkField)
    {
        $id = ucfirst($attributeCode);
        $mainTable = $this->_commonHelper()->getDatabase()->getTableName("{$mainTable}");
        $entityValue = $this->_commonHelper()->getDatabase()->getTableName("{$mainTable}_{$type}");
        $eavEntityTypeTable = $this->_commonHelper()->getDatabase()->getTableName("eav_entity_type");
        $eavEntityAttribute = $this->_commonHelper()->getDatabase()->getTableName("eav_attribute");

        $this->getSelect()
            ->joinInner(array('comCustom'.$id=>$mainTable), "main_table.{$linkField} = comCustom{$id}.entity_id", array())
            ->joinInner(array('comCustomEavEntType'.$id=>$eavEntityTypeTable), "comCustomEavEntType{$id}.entity_type_code = '{$entityType}'", array())
            ->joinInner(array('comCustomEavAttr'.$id=>$eavEntityAttribute), "comCustomEavEntType{$id}.entity_type_id = comCustomEavAttr{$id}.entity_type_id AND comCustomEavAttr{$id}.attribute_code = '{$attributeCode}'", array())
            ;

        $filter = "comCustom{$id}.entity_id = comCustomEavValue{$id}.entity_id AND comCustomEavValue{$id}.attribute_id = comCustomEavAttr{$id}.attribute_id";

        if ($storeId !== null){
            $filter .= " AND comCustomEavValue{$id}.store_id = '{$storeId}'";
        }

        $this->getSelect()
            ->joinInner(array('comCustomEavValue'.$id=>$entityValue), $filter, array())
        ;

        $this->_OrWhereAdd("comCustomEavValue{$id}.value LIKE '%{$value}%'");
    }

    protected function _OrWhereReset()
    {
        $this->_orWhere = array();
        return $this;
    }

    protected function _OrWhereAdd($where)
    {
        $this->_orWhere[] = $where;
        return $this;
    }

    protected function _OrWhereGet()
    {
        return new Zend_Db_Expr("(".implode(" OR ", $this->_orWhere).")");
    }

    /**
     * Add Common Product Name Filter by Order Id
     *
     * @param $value
     * @return Magpleasure_Common_Model_Resource_Collection_Abstract
     */
    public function addCommonCustomerId($value)
    {
        $this->_OrWhereReset();
        $this->_addEntityValueToFilter('customer', 'customer_entity', 'varchar', null, 'firstname', $value, 'customer_id');
        $this->_addEntityValueToFilter('customer', 'customer_entity', 'varchar', null, 'lastname', $value, 'customer_id');
        $this->getSelect()->where($this->_OrWhereGet());

        return $this;
    }

    /**
     * Group By
     *
     * @param string $field
     * @return Magpleasure_Common_Model_Resource_Collection_Abstract
     */
    public function groupByField($field)
    {
        $this->getSelect()->group("main_table.{$field}");
        return $this;
    }

    /**
     * Collect Values by some FieldName
     *
     * @param $fieldName
     * @return array
     */
    protected function _collectValuesByField($fieldName)
    {
        $result = array();
        foreach ($this as $item){
            $result[] = $item->getData($fieldName);
        }
        return $result;
    }

    protected function _flush($ids = null)
    {
        try {
            $write = $this->_commonHelper()->getDatabase()->getWriteConnection();
            $write->beginTransaction();
            $tableName = $this->getMainTable();
            $where = "";
            if ($ids !== null){
                $fieldName = $this->getResource()->getIdFieldName();
                $ids = implode(",", $ids);
                $where = "{$fieldName} IN ({$ids})";
            }
            $write->delete($tableName, $where);
            $write->commit();

        } catch (Exception $e){
            $this->_commonHelper()->getException()->logException($e);
        }
        return $this;
    }

    /**
     * Flush Selected Data
     * Delete selected items directly from database
     *
     * @return $this
     */
    public function flushSelected()
    {
        $ids = $this->getAllIds();
        if (is_array($ids) && count($ids)){
            $this->_flush($ids);
        }
        return $this;
    }

    /**
     * Flush All Records
     * Delete all items directly from database
     *
     * @return $this
     */
    public function flushAll()
    {
        $this->_flush();
        return $this;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->getResource()->getUseStoreLabels()){
            foreach ($this as $item){
                /** @var $item Magpleasure_Common_Model_Abstract */
                $item->load($item->getId());
            }
        }

        return $this;
    }
}
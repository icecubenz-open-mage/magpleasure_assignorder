<?php

/**
 * Extracted from - Magpleasure_Common_Model_Resource_Collection_Abstract
 *
 * Abstract Resource Collection
 */
class Magpleasure_Assignorder_Model_Mysql4_History_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
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

    protected function _flush($ids = null)
    {
        try {
            /** @var $resource Mage_Core_Model_Resource */
            $resource = Mage::getSingleton('core/resource');
            $write = $resource->getConnection('core_write');
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
            Mage::logException($e);
        }
        return $this;
    }
}

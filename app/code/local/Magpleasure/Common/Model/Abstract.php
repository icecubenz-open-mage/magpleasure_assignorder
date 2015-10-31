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

/** @method Magpleasure_Common_Model_Resource_Abstract getResource() */
class Magpleasure_Common_Model_Abstract extends Mage_Core_Model_Abstract
{

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
     * Load Abstract Model by few key fields
     *
     * @param array $data
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    public function loadByFewFields(array $data)
    {
        $this->getResource()->loadByFewFields($this, $data);
        return $this;
    }

    /**
     * Retrieves Entity Label
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLabel($storeId = null)
    {
        if ($this->getResource()->getUseStoreLabels()){
            if (!$storeId){
                $storeId = Mage::app()->getStore()->getId();
            }

            return $this->getData('store_label_'.$storeId) ? $this->getData('store_label_'.$storeId) : $this->getData('label');
        } else {
            return parent::getLabel();
        }
    }

    /**
     * Check object state (true - if it is object without id on object just created)
     * This method can help detect if object just created in _afterSave method
     * problem is what in after save onject has id and we can't detect what object was
     * created in this transaction
     *
     * @param bool $flag
     * @return bool
     */
    public function isObjectNew($flag=null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

    protected function _afterDeleteCommitIndexProcess()
    {
        if ($type = $this->_commonHelper()->getSearchCore()->getSearchableType($this->getResourceName())){

            /** @var $indexer Mage_Index_Model_Indexer */
            $indexer = Mage::getSingleton('index/indexer');
            $indexer->processEntityAction($this, $type->getTypeCode(), Mage_Index_Model_Event::TYPE_DELETE);
        }
    }

    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();

        if ($this->_commonHelper()->isSearchCoreEnabled()){
            $this->_afterDeleteCommitIndexProcess();
        }
    }

    protected function _afterSaveIndexerProcess()
    {
        if ($type = $this->_commonHelper()->getSearchCore()->getSearchableType($this->getResourceName())){

            /** @var $indexer Mage_Index_Model_Indexer */
            $indexer = Mage::getSingleton('index/indexer');
            $indexer->processEntityAction($this, $type->getTypeCode(), Mage_Index_Model_Event::TYPE_SAVE);
        }

        return $this;
    }

    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->_commonHelper()->isSearchCoreEnabled()){
            $this->_afterSaveIndexerProcess();
        }
    }
}
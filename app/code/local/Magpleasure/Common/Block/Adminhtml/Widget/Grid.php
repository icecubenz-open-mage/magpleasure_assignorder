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

class Magpleasure_Common_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Abstract Collection
     *
     * @return Magpleasure_Common_Model_Resource_Collection_Abstract
     */
    public function getCollection()
    {
        return parent::getCollection();
    }

    protected function _filterCommonProductName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addCommonProductName($value);
    }

    protected function _filterCommonSalesOrderId($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addCommonSalesOrderId($value);
    }

    protected function _filterCommonCmsBlock($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addCommonCmsBlockId($value);
    }

    protected function _filterCommonCustomerId($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addCommonCustomerId($value);
    }

    protected function _getBeforeGridHtml(){}

    protected function _getAfterGridHtml(){}

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if ($this->getRequest()->getParam('isAjax')){
            return $html;
        }
        return $this->_getBeforeGridHtml().$html.$this->_getAfterGridHtml();
    }
}


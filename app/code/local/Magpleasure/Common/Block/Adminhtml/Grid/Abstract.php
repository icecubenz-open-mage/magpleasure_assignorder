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
 * @copyright  Copyright (c) 2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Common_Block_Adminhtml_Grid_Abstract extends Mage_Adminhtml_Block_Template
{
    protected $_columns = array();
    protected $_uid = "magpleasure_grid";
    protected $_nameApp = "magpleasure.grid.data";
    protected $_options = array();
    protected $_controller_name = '';

    public function addColumn($column)
    {
        if (is_array($column)) {
            if (isset($column['sort_direction'])==''){
                $column['sort_direction'] = 0;
            }
            if (isset($column['can_sort'])==''){
                $column['can_sort'] = true;
            }
            if (isset($column['editing'])==''){
                $column['can_edit'] = false;
            }
            if (isset($column['editor'])==''){
                $column['editor'] = array();
            }

            $this->_columns[] = $column;
        } else {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
        }
    }

    protected function initializationOptions()
    {
        $this->_options['can_filter'] = false;
        $this->_options['can_sort'] = false;
        $this->_options['auto_load'] = true;
        $this->_options['can_pager'] = true;
        $this->_options['controller'] = array(
            'url' => $this->getBaseUrl() . $this->getRequest()->getRouteName() . '/' . $this->_controller_name . '/',
            'refresh' => "refresh",
            'save' => "save",
            'delete' => "delete",
        );
    }

    public function __construct()
    {
        parent::__construct();
        $this->_controller_name = $this->getRequest()->getControllerName();
        $this->initializationOptions();
        $this->setTemplate("magpleasure/grid.phtml");
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    public function getColumnsList()
    {
        return Zend_Json::encode($this->_columns);
    }

    public function columnCount()
    {
        return count($this->_columns);
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function getBeforeGridHtml()
    {
        return '';
    }

    public function getAfterGridHtml()
    {
        return '';
    }

    public function getFooterHtml(){
        return '';
    }

}
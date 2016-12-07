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

class Magpleasure_Assignorder_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('assignorderGrid');
        $this->setDefaultSort('customer_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    /**
     * Helper
     *
     * @return Magpleasure_Assignorder_Helper_Data
     */
    public function _helper()
    {
        return Mage::helper('assignorder');
    }

    public function getOrder()
    {
        if ($orderId = $this->getRequest()->getParam('order_id')) {

            if ($order = $this->_helper()->_order()->getOrder($orderId)) {
                return $order;
            }
        }
        return false;
    }

    protected function _prepareCollection()
    {
        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        if ($order = $this->getOrder()) {
            if ($customerId = $order->getCustomerId()) {
                $collection->addFieldToFilter('entity_id', array('neq' => $customerId));
            }
            if (Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
                $collection->addFieldToFilter('website_id', array('eq' => $order->getStore()->getWebsiteId()));
            }
        }
        else if ($websiteId = Mage::getSingleton('adminhtml/session')->getAssignorderWebsiteId()) {
            $collection->addFieldToFilter('website_id', array('eq' => $websiteId));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Helper
     *
     * @return Magpleasure_Assignorder_Helper_Data
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header' => Mage::helper('customer')->__('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ));

        $this->addColumn('Telephone', array(
            'header' => Mage::helper('customer')->__('Telephone'),
            'width' => '100',
            'index' => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header' => Mage::helper('customer')->__('ZIP'),
            'width' => '90',
            'index' => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header' => Mage::helper('customer')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header' => Mage::helper('customer')->__('State/Province'),
            'width' => '100',
            'index' => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header' => Mage::helper('customer')->__('Customer Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('customer')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
            ));
        }


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return "javascript: assignorderRowClick();";
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/customerGrid');
    }
}

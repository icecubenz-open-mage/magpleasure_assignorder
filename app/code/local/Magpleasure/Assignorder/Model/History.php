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

class Magpleasure_Assignorder_Model_History extends Magpleasure_Common_Model_Abstract
{
    protected $_order = null;
    protected $_details = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('assignorder/history');
    }

    public function rollback()
    {
        $order = $this->getOrder();
        if ($order) {
            foreach ($this->getDetails() as $detail) {
                $detail->rollback($order);
            }
            $order->save();

            $this->delete();
        }
        return $this;
    }

    public function canRollback()
    {
        if ($lastItem = $this->getOrder()->getAssignmentHistory()->getLastItem()) {
            return ($lastItem->getId() == $this->getId());
        }
        return false;
    }

    /**
     * Order
     *
     * @return Magpleasure_Assignorder_Model_Order|boolean
     */
    public function getOrder()
    {
        if (!$this->_order) {
            if ($orderId = $this->getOrderId()) {

                $order = Mage::getModel('assignorder/order')->load($orderId);
                $this->_order = $order;

            } else {
                return false;
            }
        }
        return $this->_order;
    }

    public function applyOrder(Mage_Sales_Model_Order $order, $sendEmail = false)
    {
        $this->_order = $order;
        $timestamp = new Zend_Date();
        $this
            ->setOrderId($order->getId())
            ->setIsNotified($sendEmail ? 1 : 0)
            ->setAssignTime($timestamp->toString(Zend_Date::ISO_8601))
            ->save();

        return $this;
    }

    public function addDetails($key, $from = null, $to = null)
    {
        $detail = Mage::getModel('assignorder/detail');
        $detail->setHistoryId($this->getId())
            ->setDataKey($key)
            ->setFrom($from)
            ->setTo($to)
            ->save();
        return $this;
    }

    public function getDetails()
    {
        if (!$this->_details) {
            /** @var $collection  Magpleasure_Assignorder_Model_Mysql4_Detail_Collection */
            $collection = Mage::getModel('assignorder/detail')->getCollection();
            $collection->addFieldToFilter('history_id', $this->getId());
            $this->_details = $collection;
        }
        return $this->_details;
    }

    public function hasDetails()
    {
        return !!$this->getDetails()->getSize();
    }

    public function getAssignTime()
    {
        $timezone = Mage::app()->getStore()->getConfig('general/locale/timezone');
        $date = new Zend_Date($this->getData('assign_time'), Zend_Date::ISO_8601);
        $date->setTimezone($timezone);
        return $date->toString(Zend_Date::DATETIME_MEDIUM);
    }

    public function getUrl($routePath = null, $routeParams = null)
    {
        /** @var $urlModel Mage_Adminhtml_Model_Url */
        $urlModel = Mage::getSingleton('adminhtml/url');

        return $urlModel->getUrl($routePath, $routeParams);
    }

    public function getCustomerUrl()
    {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $this->getCustomer()->getId()));
    }

    public function getCustomer()
    {
        $customerId = null;
        foreach ($this->getDetails() as $detail) {
            if ($detail->getDataKey() == 'customer_id') {
                $customerId = $detail->getTo();
                break;
            }
        }
        if ($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            return $customer;
        }
        return new Varien_Object();
    }

    public function getFromData()
    {
        $data = array();

        foreach ($this->getDetails() as $detail){
            $data[$detail->getDataKey()] = $detail->getFrom();
        }

        return $data;
    }
}
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

class Magpleasure_Assignorder_Block_Adminhtml_Customer_Info extends Mage_Adminhtml_Block_Template
{
    /**
     * Helper
     *
     * @return Magpleasure_Assignorder_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('assignorder');
    }

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('assignorder/order/info.phtml');
    }

    public function getOrderId()
    {
        return $this->_helper()->_order()->getOrder()->getId();
    }

    public function getIncrementId()
    {
        $orderId = null;
        if (!$this->isMultiOrders()){
            $orderId = $this->getOrderIds();
        }

        if ($order = $this->_helper()->_order()->getOrder($orderId)){
            return $order->getIncrementId();
        } else {
            return false;
        }
    }

    public function getAssignUrl()
    {
        return $this->getUrl('assignorder/order/assignToCustomer');
    }

    public function getOrderIds()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        if ($orderIds && is_array($orderIds)){
            return implode(",", $orderIds);
        } else {
            return $this->getRequest()->getParam('order_id');
        }
    }

    public function getConfSendEmail()
    {

        $session = Mage::getSingleton('adminhtml/session');
        return $session->getData('assignorder_send_email') && $this->_helper()->configNotificationEnabled();
    }

    public function getConfOverwriteName()
    {
        $session = Mage::getSingleton('adminhtml/session');
        return !!$session->getData('assignorder_overwrite_name');
    }

    public function getOrdersLabel()
    {
        if ($this->isMultiOrders()) {
            return $this->__("%s Orders", "{{order_count}}");
        } else {
            return $this->__("Order: %s", "#{{increment_id}}");
        }
    }

    public function isMultiOrders()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        return $orderIds && is_array($orderIds) && (count($orderIds) > 1);
    }

    public function getOrdersCount()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        return count($orderIds);
    }
}
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

class Magpleasure_Assignorder_Adminhtml_Assignorder_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_helper()->isAllowed();
    }

    /**
     * Helper
     *
     * @return Magpleasure_Assignorder_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('assignorder');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->_helper()->__('Assign Order to Customer'), $this->_helper()->__('Assign Order to Customer'));

        return $this;
    }

    public function customerSelectAction()
    {
        if ($orderId = $this->getRequest()->getParam('order_id')) {
            $orderIds = array($orderId);
        } else {
            $orderIds = $this->getRequest()->getPost('order_ids');
        }
        if ( ! $orderIds){
            $this->_redirect('adminhtml/sales_order/index');
            return;
        }

        // If customers are unique per website then ensure that orders are only assigned from the customer's website
        Mage::getSingleton('adminhtml/session')->unsAssignorderWebsiteId();
        if ($orderIds && Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
            $collection = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('entity_id', array('in' => $orderIds));
            $websiteId = NULL;
            foreach ($collection as $order) { /* @var $order Mage_Sales_Model_Order */
                if ($websiteId === NULL) {
                    $websiteId = $order->getStore()->getWebsiteId();
                }
                else if ($websiteId !== $order->getStore()->getWebsiteId()) {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('All orders to assign must be from the same website.'));
                    $websiteId = NULL;
                    break;
                }
            }
            if ($websiteId) {
                Mage::getSingleton('adminhtml/session')->setAssignorderWebsiteId($websiteId);
            }
        }
        $this->_initAction()
            ->renderLayout();
    }

    public function customerGridAction()
    {
        $grid = $this->getLayout()->createBlock('assignorder/adminhtml_customer_grid');
        if ($grid) {
            $this->getResponse()->setBody($grid->toHtml());
        }
    }

    public function assignToCustomerAction()
    {
        $customerId = $this->getRequest()->getPost('customer_id');
        $sendEmail = $this->getRequest()->getPost('send_email') ? 1 : 0;
        $overwriteName = $this->getRequest()->getPost('overwrite_name') ? 1 : 0;
        $orderIds = $this->getRequest()->getParam('order_ids');
        $orderIds = explode(",", $orderIds);
        $websiteId = Mage::getSingleton('adminhtml/session')->getAssignorderWebsiteId(TRUE);

        $session = Mage::getSingleton('adminhtml/session');
        $session->setData('assignorder_overwrite_name', $overwriteName);

        if ($this->_helper()->configNotificationEnabled()){
            $session->setData('assignorder_send_email', $sendEmail);
        }

        if ($customerId) {

            $success = 0;
            $error = 0;

            foreach ($orderIds as $orderId) {

                if ($customerId && $orderId) {

                    $order = $this->_helper()->_order()->getOrder($orderId);
                    if (Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
                        // Confirm all order belong to same website as customer
                        if ($websiteId != $order->getStore()->getWebsiteId()) {
                            $error++;
                            continue;
                        }
                    }
                    $order->assignToCustomer($customerId, $overwriteName, $sendEmail);

                    $success++;

                } else {
                    $error++;
                }
            }

            if ( count($orderIds) > 1 ) {

                if ($success){
                    $this->_getSession()->addSuccess($this->_helper()->__("%s orders were successfully assigned to the customer.", $success));
                }

                if ($error){
                    $this->_getSession()->addError($this->_helper()->__("%s orders were not be updated due to some error.", $error));
                }

                $this->_redirect('adminhtml/sales_order/index');

            } else {
                $this->_getSession()->addSuccess($this->_helper()->__("Order was successfully assigned to customer."));
                $this->_redirect('adminhtml/sales_order/view', array('order_id' => $this->getRequest()->getParam('order_ids')));
            }

        } else {

            $this->_getSession()->addError($this->_helper()->__("Some data was missed or your session was expired. Please try again."));

            if ($orderId = $this->getRequest()->getParam('order_id')){

                $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
            } else {

                $this->_redirect('adminhtml/sales_order/index');
            }
        }

        return;
    }

    public function rollbackAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $order = $this->_helper()->_order()->getOrder($orderId);
            if ($history = $order->getAssignmentHistory()->getLastItem()) {
                try {
                    $history->rollback();
                    $this->_getSession()->addSuccess($this->_helper()->__("The assignment was successfully rolled back."));
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->_helper()->__("Can't rollback this order."));
                }
            } else {
                $this->_getSession()->addError($this->_helper()->__("Can't rollback this order."));
            }
        } else {
            $this->_getSession()->addError($this->_helper()->__("Some data was missed or your session was expired. Please try again."));
        }
        $this->_redirectReferer();
    }

}

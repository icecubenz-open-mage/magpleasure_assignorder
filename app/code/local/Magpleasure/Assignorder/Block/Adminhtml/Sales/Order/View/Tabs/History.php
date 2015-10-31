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

class Magpleasure_Assignorder_Block_Adminhtml_Sales_Order_View_Tabs_History extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('assignorder/order/view/tab/history.phtml');
    }

    /**
     * Current Order
     *
     * @return Magpleasure_Assignorder_Model_Order
     */
    protected function _getOrder()
    {
        $id = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getModel('assignorder/order')->load($id);
        return $order;
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

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__("History of Assignment");
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__("History of Assignment");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_getOrder()->hasAssignmentHistory();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    public function getHistory()
    {
        return $this->_getOrder()->getAssignmentHistory();
    }

    public function getOrderId()
    {
        return $this->_getOrder()->getId();
    }

    public function getIncrementId()
    {
        return $this->_getOrder()->getIncrementId();
    }

    public function getCustomerName()
    {
        return $this->_getOrder()->getAssignmentHistory()->getLastItem()->getCustomer()->getName();
    }

    public function getCustomerEmail()
    {
        return $this->_getOrder()->getAssignmentHistory()->getLastItem()->getCustomer()->getEmail();
    }

    public function getRollbackUrl()
    {
        return $this->getUrl('assignorder/order/rollback', array('order_id' => '{{order_id}}'));
    }

    public function getRollbackButtonHtml()
    {
        /** @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button');

        $button->addData(array(
            'title' => $this->_helper()->__("Rollback"),
            'label' => $this->_helper()->__("Rollback"),
            'onclick' => 'rollbackOrder();',
            'class' => 'scalable rollback delete',
        ));

        return $button->toHtml();
    }

}

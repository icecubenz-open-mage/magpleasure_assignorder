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

class Magpleasure_Assignorder_Model_Order extends Mage_Sales_Model_Order
{
    protected $_assignmentHistoryCollection = null;

    /**
     * Customer
     *
     * @param $customerId
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        return $customer;
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

    public function getNameParts()
    {
        return array(
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
        );
    }

    public function getPreviousCustomerName()
    {
        $nameParts = array();
        /** @var Magpleasure_Assignorder_Model_History $lastItem */
        $lastItem = $this->getAssignmentHistory()->getLastItem();
        if ($lastItem && $lastItem->hasDetails()){

            $from = $lastItem->getFromData();

            foreach ($this->getNameParts() as $key){
                $fromKey = 'customer_'.$key;
                if (isset($from[$fromKey]) && $from[$fromKey]){
                    $nameParts[] = $from[$fromKey];
                }
            }

            return implode(" ", $nameParts);
        }
        return false;
    }

    /**
     * Assign order to customer
     *
     * @param $customerId
     * @param bool $overwriteName
     * @param bool $sendEmail
     * @return Magpleasure_Assignorder_Model_Order
     */
    public function assignToCustomer($customerId, $overwriteName = true, $sendEmail = true)
    {
        $customer = $this->_getCustomer($customerId);

        /** @var $history Magpleasure_Assignorder_Model_History */
        $history = Mage::getModel('assignorder/history');
        $history->applyOrder($this, $sendEmail);
        $prevIsGuest = $this->getCustomerIsGuest();

        $history
            ->addDetails('customer_id', $this->getCustomerId(), $customerId)
            ->addDetails('customer_email', $this->getCustomerEmail(), $customer->getEmail())
            ->addDetails('customer_is_guest', $this->getCustomerIsGuest(), $customer->getIsGuest());

        $this
            ->setCustomerId($customerId)
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerIsGuest(0);

        if ($overwriteName) {

            $nameParts = $this->getNameParts();
            foreach ($nameParts as $nameKey) {
                $dataKey = 'customer_' . $nameKey;
                $history->addDetails($dataKey, $this->getData($dataKey), $customer->getData($nameKey));
                $this->setData($dataKey, $customer->getData($nameKey));
            }
        }

        if ($sendEmail) {
            $this->_helper()->_notify()->notifyCustomer($this, $customerId, $prevIsGuest);
        }

        $this->save();
        return $this;
    }

    public function isGuestOrder()
    {
        return $this->getCustomerIsGuest();
    }

    /**
     * History of Assignment
     *
     * @return Magpleasure_Assignorder_Model_Mysql4_History_Collection
     */
    public function getAssignmentHistory()
    {
        if (!$this->_assignmentHistoryCollection) {
            /** @var $collection  Magpleasure_Assignorder_Model_Mysql4_History_Collection */
            $collection = Mage::getModel('assignorder/history')->getCollection();
            $collection
                ->addFieldToFilter('order_id', $this->getId())
                ->setOrder('assign_time', 'asc');

            $this->_assignmentHistoryCollection = $collection;
        }
        return $this->_assignmentHistoryCollection;
    }

    /**
     * Has Assigment History
     *
     * @return bool
     */
    public function hasAssignmentHistory()
    {
        return !!$this->getAssignmentHistory()->getSize();
    }
}

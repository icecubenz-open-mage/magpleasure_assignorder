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

class Magpleasure_Assignorder_Helper_Order extends Mage_Core_Helper_Abstract
{
    protected function _getRequestOrderId()
    {
        return Mage::app()->getRequest()->getParam('order_id');
    }

    public function isGuestOrder($orderId = null)
    {
        return !$this->getOrder($orderId)->getCustomerId();
    }

    /**
     * Order
     *
     * @param null $orderId
     * @return Magpleasure_Assignorder_Model_Order|null
     */
    public function getOrder($orderId = null)
    {
        if (!$orderId) {
            $orderId = $this->_getRequestOrderId();
        }

        $order = Mage::getModel('assignorder/order')->load($orderId);
        if ($order->getId()) {
            return $order;
        } else {
            return null;
        }
    }
}
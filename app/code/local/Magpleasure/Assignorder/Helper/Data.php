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

class Magpleasure_Assignorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Order Helper
     *
     * @return Magpleasure_Assignorder_Helper_Order
     */
    public function _order()
    {
        return Mage::helper('assignorder/order');
    }

    /**
     * Order Helper
     *
     * @return Magpleasure_Assignorder_Helper_Notify
     */
    public function _notify()
    {
        return Mage::helper('assignorder/notify');
    }

    public function configNotificationEnabled()
    {
        return Mage::getStoreConfig('assignorder/notification/enabled');
    }

    /**
     * Is Allowed Action
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/assignorder');
    }

    /**
     * Retrieves is Enabled
     *
     * @return boolean
     */
    public function extEnabled()
    {
        return !Mage::getStoreConfig('advanced/modules_disable_output/Magpleasure_Assignorder');
    }
}

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

class Magpleasure_Assignorder_Model_Observer
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

    /**
     * Backend URL Model
     *
     * @return Mage_Adminhtml_Model_Url
     */
    protected function _getBackendUrlModel()
    {
        return Mage::getSingleton("adminhtml/url");
    }

    public function generateBlockAfter($event)
    {
        $block = $event->getBlock();

        # Order View
        if ($block && ($block->getNameInLayout() == 'sales_order_edit')) {
            if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {

                /** @var Magpleasure_Assignorder_Model_Order $order */
                $order = $this->_helper()->_order()->getOrder();

                if ($order->isGuestOrder() && $this->_helper()->isAllowed()) {

                    $url = $this->_getBackendUrlModel()->getUrl('assignorder/order/customerSelect', array(
                        'order_id' => $order->getId(),
                    ));

                    $block->addButton('assignOrder', array(
                        'label' => $this->_helper()->__("Assign to Customer"),
                        'onclick' => "window.location = '{$url}';",
                        'class' => 'assign-order',
                    ));

                } else {

                    $url = $this->_getBackendUrlModel()->getUrl('assignorder/order/customerSelect', array(
                        'order_id' => $order->getId(),
                    ));

                    $block->addButton('assignOrder', array(
                        'label' => $this->_helper()->__("Assign to Other Customer"),
                        'onclick' => "window.location = '{$url}';",
                        'class' => 'assign-order',
                    ));
                }

            }
        }
    }

    public function massActionOption($observer)
    {
        if (!$this->_helper()->extEnabled()) {
            return;
        }

        $block = $observer->getBlock();

        $allowedNames = array(
            'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction',
            'Enterprise_Salesarchive_Block_Widget_Grid_Massaction',
            'Mage_Adminhtml_Block_Widget_Grid_Massaction',
            'Amasty_Oaction_Block_Adminhtml_Widget_Grid_Massaction',
        );

        if ($block && in_array(get_class($block), $allowedNames)) {

            $allowedControllerNames = array(
                'orderspro_order',
                'sales_order',
            );

            if ( in_array($block->getRequest()->getControllerName(), $allowedControllerNames) ) {

                $backendUrl = Mage::getSingleton('adminhtml/url');
                $block->addItem('assign', array(
                    'label' => $this->_helper()->__("Assign to Customer"),
                    'url' => $backendUrl->getUrl('assignorder/order/customerSelect')
                ));
            }
        }
    }
}
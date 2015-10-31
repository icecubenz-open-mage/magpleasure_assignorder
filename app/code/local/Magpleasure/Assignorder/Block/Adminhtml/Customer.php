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

class Magpleasure_Assignorder_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
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

    public function initHeaders()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        if($orderIds){
            $this->_headerText = $this->_helper()->__('Assign %s Selected Order(s) to Customer', count($orderIds));
        } else {
            $this->_headerText = $this->_helper()->__('Assign Order (#%s) to Customer', $this->_helper()->_order()->getOrder()->getIncrementId());
        }
    }

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer';
        $this->_blockGroup = 'assignorder';

        $this->initHeaders();

        parent::__construct();
        $this->removeButton('add');

        if ($this->getRequest()->getParam('order_ids')){

            $backUrl = $this->getUrl('adminhtml/sales_order/index');
        } else {

            $backUrl = $this->getUrl('adminhtml/sales_order/view', array(
                'order_id' => $this->getRequest()->getParam('order_id'),
            ));
        }

        $resetUrl = $this->getUrl('adminhtml/assignorder_order/customerSelect', array(
            'order_id' => $this->getRequest()->getParam('order_id'),
        ));
        $this->addButton('back', array(
            'label' => $this->_helper()->__("Back"),
            'onclick' => "window.location = '{$backUrl}';",
            'class' => 'back',
        ));

        $this->addButton('reset', array(
            'label' => $this->_helper()->__("Reset"),
            'onclick' => "window.location = '{$resetUrl}';",
            'class' => '',
        ));

    }
}

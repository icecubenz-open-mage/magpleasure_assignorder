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
 * @package    Magpleasure_Common
 * @version    master
 * @copyright  Copyright (c) 2012-2015 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Block_Adminhtml_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Adding child block with specified child's id.
     *
     * @param string $childId
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    protected function _addButtonChildBlock($childId)
    {
        /** @var Magpleasure_Common_Block_Adminhtml_Widget_Button $block */
        $block = $this->getLayout()->createBlock('magpleasure/adminhtml_widget_button');
        $this->setChild($childId, $block);
        return $block;
    }
}
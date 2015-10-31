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

class Magpleasure_Common_Block_Widget_Button extends Mage_Core_Block_Template
{
    public function getType()
    {
        return ($type=$this->getData('type')) ? $type : 'button';
    }

    public function getOnClick()
    {
        if (!$this->getData('on_click')) {
            return $this->getData('onclick');
        }
        return $this->getData('on_click');
    }

    public function getTitle()
    {
        return $this->getData('title') ? $this->getData('title') : $this->getData('label');
    }

    protected function _toHtml()
    {
        $html = $this->getBeforeHtml().'<button '
            . ($this->getId()?' id="'.$this->getId() . '"':'')
            . ($this->getElementName()?' name="'.$this->getElementName() . '"':'')
            . ($this->getTitle()?' title="'.$this->getTitle() . '"':'')
            . ' title="'.$this->getTitle() . '"'
            . ' type="'.$this->getType() . '"'
            . ' class="button ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : '') . '"'
            . ' onclick="'.$this->getOnClick().'"'
            . ' style="'.$this->getStyle() .'"'
            . ' '.$this->getAdditionalAttributes() .' '
            . ($this->getValue()?' value="'.$this->getValue() . '"':'')
            . ($this->getDisabled() ? ' disabled="disabled"' : '')
            . '><span><span>' .$this->getLabel().'</span></span></button>'.$this->getAfterHtml();

        return $html;
    }

}
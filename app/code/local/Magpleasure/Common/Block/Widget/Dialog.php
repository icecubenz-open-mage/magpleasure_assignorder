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

class Magpleasure_Common_Block_Widget_Dialog extends Mage_Core_Block_Template
{
    const FKEY_JS_OBJECT_NAME = '_jsObjectName';

    protected $_afterButtonBlocks = array();
    protected $_beforeButtonBlocks = array();
    protected $_leftButtonBlocks = array();
    protected $_rightButtonBlocks = array();
    protected $_buttons = array();
    protected $_buttonsAlign = 'center';

    protected $_jsObjectName = 'unknown';

    protected function _construct()
    {
        parent::_construct();
        $this->_restoreForwardedData();
        $this->setTemplate('magpleasure/widget/dialog.phtml');
    }

    /**
     * Set Up Buttons Align
     *
     * @param string $value
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    public function setButtonsAlign($value)
    {
        $this->_buttonsAlign = $value;
        return $this;
    }

    protected function _restoreForwardedData()
    {
        $data = $this->getRequest()->getParam('forward_data');
        if ($data){
            try {
                $data = $this->_commonHelper()->getCore()->urlDecode($data);
                $data = unserialize($data);
                if ($data && is_array($data)){
                    foreach ($data as $key => $value){
                        if ($key && ($key[0] == '_')){
                            if ($key == self::FKEY_JS_OBJECT_NAME){
                                $this->_jsObjectName = $value;
                            }
                        } else {
                            /** @var $model Mage_Core_Model_Abstract */
                            $model = Mage::getModel("catalog/{$key}")->load($value);
                            if ($model->getId()){
                                Mage::register("current_{$key}", $model, true);
                            }
                        }
                    }
                }

            } catch (Exception $e){
                $this->_commonHelper()->getException()->logException($e);
            }
        }
        return $this;
    }

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Retrieves Buttons Align
     *
     * @return string
     */
    public function getButtonsAlign()
    {
        return $this->_buttonsAlign;
    }

    /**
     * Retrieves block instance
     *
     * @param string|Mage_Core_Block_Abstract $block
     * @return Mage_Core_Block_Abstract
     */
    protected function _getBlockInstance($block)
    {
        if (is_string($block)){
            return $this->getLayout()->createBlock($block);
        } elseif ($block instanceof Mage_Core_Block_Abstract){
            return $block;
        } else {
            return false;
        }
    }

    /**
     * Add block before buttond
     *
     * @param $block
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    public function addBeforeButtonsBlock($block)
    {
        if ($block = $this->_getBlockInstance($block)){
            $this->_beforeButtonBlocks[] = $block;
        }
        return $this;
    }

    /**
     * Add block before buttond
     *
     * @param $block
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    public function addBeforButtonsBlock($block)
    {
        return $this->addBeforeButtonsBlock($block);
    }

    /**
     * Add block after buttons
     *
     * @param $block
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    public function addAfterButtonsBlock($block)
    {
        if ($block = $this->_getBlockInstance($block)){
            $this->_afterButtonBlocks[] = $block;
        }
        return $this;
    }

    /**
     * Add left block
     *
     * @param $block
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    public function addLeftButtonsBlock($block)
    {
        if ($block = $this->_getBlockInstance($block)){
            $this->_leftButtonBlocks[] = $block;
        }
        return $this;
    }

    /**
     * Add left block
     *
     * @param $block
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    public function addRightButtonsBlock($block)
    {
        if ($block = $this->_getBlockInstance($block)){
            $this->_rightButtonBlocks[] = $block;
        }
        return $this;
    }

    public function addButton($name, $data)
    {
        $button = $this->_getBlockInstance('magpleasure/widget_button');
        if ($button){
            $button->addData($data);
            $this->_buttons[$name] = $button;
        }
        return $this;
    }

    protected function _arrayToHtml(array $array)
    {
        if (count($array)){
            $html = "";
            foreach ($array as $block){
                /** @var $block Mage_Core_Block_Abstract */
                $html .= $block->toHtml();
            }
            return $html;
        }
        return false;
    }

    public function getButtonHtml()
    {
        if (count($this->_buttons)){
            $html = "";
            foreach ($this->_buttons as $name=>$block){
                /** @var $block Mage_Core_Block_Abstract */
                $html .= $block->toHtml();
            }
            return $html;
        }
        return false;
    }

    public function getAfterButtonHtml()
    {
        return $this->_arrayToHtml($this->_afterButtonBlocks);
    }

    public function getBeforeButtonHtml()
    {
        return $this->_arrayToHtml($this->_beforeButtonBlocks);
    }

    public function getRightHtml()
    {
        return $this->_arrayToHtml($this->_rightButtonBlocks);
    }

    public function getLeftHtml()
    {
        return $this->_arrayToHtml($this->_leftButtonBlocks);
    }

    public function getPostAction()
    {
        $postUrl = $this->getRequest()->getParam('post_url');
        if ($postUrl){
            $postUrl = $this->_commonHelper()->getCore()->urlDecode($postUrl);
            return $postUrl;
        }
        return $this->getUrl('*/*/post');
    }

    public function getJsObjectName()
    {
        return $this->_jsObjectName;
    }

    public function getUniqId()
    {
        return md5(microtime());
    }

}
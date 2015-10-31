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

class Magpleasure_Common_Block_Widget_Dialog_Wrapper extends Mage_Core_Block_Template
{
    protected $_dialogs = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('magpleasure/widget/dialog/wrapper.phtml');
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

    public function addJsObject($name, $action, $width, $height, $forwardData = null)
    {
        $this->_dialogs[] = new Varien_Object(array(
                                    'name' => $name,
                                    'action' => $action,
                                    'width' => $width,
                                    'height' => $height,
                                    'forward_data' => $forwardData,
                                 ));
    }

    protected function getDialogs()
    {
        return $this->_dialogs;
    }

    protected function _prepareDataToForward($keys)
    {
        return $data;
    }

    public function getWindowUrl(Varien_Object $dialog)
    {
        return $this->getUrl(str_replace('*', 'window', $dialog->getAction()), array(
                                                                                        'post_url' => '{{post_url}}',
                                                                                        'width'    => '{{width}}',
                                                                                        'height'   => '{{height}}',
                                                                                        'forward_data' => '{{forward_data}}',
                                                                                        'additional_data' => '{{additional_data}}',
                                                                                    ));
    }

    public function getPostUrl(Varien_Object $dialog)
    {
        $url = $this->getUrl(str_replace('*', 'post', $dialog->getAction()));
        return $this->_commonHelper()->getCore()->urlEncode($url);
    }

    /**
     * Process result
     *
     * @param $value
     * @return string
     */
    protected function _processSizeParam($value)
    {
        if (is_numeric($value)){
            $result = $value;
        } else {
            $result = Mage::getStoreConfig($value);
        }
        return $result."px";
    }

    public function getWidth(Varien_Object $dialog)
    {
        $width = $this->_processSizeParam($dialog->getWidth());
        return $this->_commonHelper()->getCore()->urlEncode($width);
    }

    public function getHeight(Varien_Object $dialog)
    {
        $height = $this->_processSizeParam($dialog->getHeight());
        return $this->_commonHelper()->getCore()->urlEncode($height);
    }

    public function getForwardData(Varien_Object $dialog)
    {
        $data = array();
        $forward = $dialog->getForwardData();
        if  ($forward){
            $keys = explode(",", $forward);
            foreach ($keys as $key){
                /** @var $instance Mage_Core_Model_Abstract */
                $instance = Mage::registry("current_{$key}");
                if ($instance && $instance->getId()){
                    $data[$key] = $instance->getId();
                }
            }
        }
        $data[Magpleasure_Common_Block_Widget_Dialog::FKEY_JS_OBJECT_NAME] = $dialog->getName();

        try {
            $data = serialize($data);
        } catch (Exception $e) {
            $data = '';
        }
        return $this->_commonHelper()->getCore()->urlEncode($data);
    }

}
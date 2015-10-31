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

class Magpleasure_Common_Controller_Adminhtml_Action extends Mage_Adminhtml_Controller_Action
{
    protected $_modelName;
    protected $_aclAllowCheckPath;
    protected $_messages = array();

    const KEY_SUCCESS = 'success';
    const KEY_ERROR = 'error';

    protected function _addMessage($action, $key, $message)
    {
        if (!isset($this->_messages[$action])){
            $this->_messages[$action] = array();
        }

        if (!isset($this->_messages[$action][$key])){
            $this->_messages[$action][$key] = "";
        }

        $this->_messages[$action][$key] = $message;

        return $this;
    }


    protected function _getPartNameFromChildren($part, &$children)
    {
        $label = false;
        if (isset($children[$part])){
            $label = $children[$part]['label'];

            if (isset($children[$part]['children'])){
                $children = $children[$part]['children'];
            }
        }
        return $label;
    }

    /**
     * Define active menu item in menu block
     *
     * @param $menuPath
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _setActiveMenu($menuPath)
    {
        parent::_setActiveMenu($menuPath);

        if (method_exists($this, '_title')){
            try {
                $labels = array();
                $parts = explode("/", $menuPath);

                /** @var $menu Mage_Adminhtml_Block_Page_Menu */
                $menu = $this->getLayout()->getBlock('menu');
                $children = $menu->getMenuArray();

                foreach ($parts as $part){
                    if ($label = $this->_getPartNameFromChildren($part, $children)){
                        $labels[] = $label;
                    } else {
                        break;
                    }
                }

                if (count($labels)){
                    $this->_title();
                    foreach ($labels as $label){
                        $this->_title($label);
                    }
                }

            } catch (Exception $e){
                $this
                    ->_commonHelper()
                    ->getException()
                    ->logException($e);
            }
        }

        return $this;
    }

    /**
     * Response for Ajax Request
     *
     * @param array $result
     */
    protected function _ajaxResponse($result = array())
    {
        return $this->_jsonResponse($result);
    }

    /**
     * JSON Response
     *
     * @param array $result
     */
    protected function _jsonResponse($result = array())
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Retrieves Messages Html
     *
     * @return string
     */
    protected function _getMessageBlockHtml()
    {
        return $this->getLayout()->getMessagesBlock()->addMessages(Mage::getSingleton('adminhtml/session')->getMessages(true))->toHtml();
    }

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }




}
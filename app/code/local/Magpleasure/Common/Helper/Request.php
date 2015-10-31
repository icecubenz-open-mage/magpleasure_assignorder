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
class Magpleasure_Common_Helper_Request extends Magpleasure_Common_Helper_Data
{
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
     * Check url to be used as internal
     *
     * @param   string $url
     * @return  bool
     */
    protected function _isUrlInternal($url)
    {
        if (strpos($url, 'http') !== false) {
            /**
             * Url must start from base secure or base unsecure url
             */
            if ((strpos($url, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    protected function _getRefererUrl()
    {
        $refererUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if ($url = Mage::app()->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = Mage::app()->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_BASE64_URL)) {
            $refererUrl = $this->_commonHelper()->getCore()->urlDecode($url);
        }
        if ($url = Mage::app()->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = $this->_commonHelper()->getCore()->urlDecode($url);
        }

        $refererUrl = $this->_commonHelper()->getCore()->escapeUrl($refererUrl);

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }
    
    public function getReferrerUrl()
    {
        return $this->_getRefererUrl();
    }

    /**
     * URL Model
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlModel()
    {
        if (Mage::app()->getLayout()->getArea() == 'adminhtml'){

            return Mage::getSingleton('adminhtml/url');
        } else {
            return Mage::getSingleton('core/url');
        }
    }

    /**
     * Retrieves IS_AJAX flag
     *
     * @return bool
     */
    public function isAjax()
    {
        $markers = array(
            'is_ajax',
            'isAjax',
            'ajax',
        );
        foreach ($markers as $key){
            if (Mage::app()->getRequest()->getParam($key)){
                return true;
            }
        }
        return false;
    }

    /**
     * Current Url
     *
     * @return string
     */
    public function getCurrentManegtoUrl()
    {
        /** @var Mage_Core_Helper_Url $urlHelper */
        $urlHelper = Mage::helper('core/url');
        return $urlHelper->getCurrentUrl();
    }

}
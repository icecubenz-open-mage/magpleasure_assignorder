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
class Magpleasure_Common_Helper_Cookie extends Mage_Core_Model_Cookie
{
    const DEFAULT_TIMEOUT = 86400;

    protected $_values = array();

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function stringToArray($string)
    {
        return $this->_stringToArray($string);
    }

    public function arrayToString($array)
    {
        return $this->_arrayToString($array);
    }

    protected function _stringToArray($string)
    {
        if ($string){
            try {
                return json_decode($string);
            } catch (Exception $e){
                $this->_commonHelper()->getException()->logException($e);
                return array();
            }
        } else {
            return array();
        }
    }

    protected function _arrayToString(array $array)
    {
        try {
            return json_encode($array);
        } catch (Exception $e) {
            $this->_commonHelper()->getException()->logException($e);
            return json_encode(array());
        }
    }

    protected function _systemTimeout()
    {
        $timeout = Mage::getStoreConfig('web/cookie/cookie_lifetime');
        return $timeout ? $timeout : self::DEFAULT_TIMEOUT;
    }

    public function addToCookie($cookieName, $value, $timeout = null)
    {
        if (!$timeout){
            $timeout = $this->_systemTimeout();
        }

        $cValue = $this->getValue($cookieName);
        $data = $this->_stringToArray($cValue);
        $data = $this->_commonHelper()->getArrays()->addUniqueValueToArray($data, $value);
        ksort($data);
        $cValue = $this->_arrayToString($data);
        $this->setValue($cookieName, $cValue, $timeout);
        return $this;
    }

    public function removeFromCookie($cookieName, $value, $timeout = null)
    {
        if (!$timeout){
            $timeout = $this->_systemTimeout();
        }

        $cValue = $this->getValue($cookieName);
        $data = $this->_stringToArray($cValue);
        $data = $this->_commonHelper()->getArrays()->removeValueFromArray($data, $value);
        ksort($data);
        $cValue = $this->_arrayToString($data);
        $this->setValue($cookieName, $cValue, $timeout);
        return $this;
    }

    public function isInCookie($cookieName, $value)
    {
        $cValue = $this->getValue($cookieName);
        $data = $this->_stringToArray($cValue);
        $result = $this->_commonHelper()->getArrays()->isValueInArray($data, $value);
        $this->setValue($cookieName, $cValue);
        return $this;
    }

    public function getAllFromCookie($cookieName)
    {
        $cValue = $this->getValue($cookieName);
        return $this->_stringToArray($cValue);
    }

    /**
     * Retrieves value from Cookie
     *
     * @param string $cookieName
     * @return string|boolean
     */
    public function getValue($cookieName)
    {
        if (!isset($this->_values[$cookieName])){
            $this->_values[$cookieName] = Mage::app()->getRequest()->getCookie($cookieName, false);
        }
        return $this->_values[$cookieName];
    }

    /**
     * Save Value in Cookie
     *
     * @param $cookieName
     * @param $value
     * @param int $timeout
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httpOnly
     * @return $this
     */
    public function setValue($cookieName, $value, $timeout = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if (is_null($path)) {
            $path = $this->getPath();
        }
        if (is_null($domain)) {
            $domain = $this->getDomain();
        }
        if (is_null($secure)) {
            $secure = $this->isSecure();
        }
        if (is_null($httpOnly)) {
            $httpOnly = $this->getHttponly();
        }

        if (!$timeout) {
            $expire = time() + $this->_systemTimeout();
        } else {
            $expire = time() + $timeout;
        }

        setcookie($cookieName, $value, $expire, $path, $domain, $secure, $httpOnly);

        return $this;
    }

    public function renewCookie($cookieName, $timeout = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if ($this->getValue($cookieName) !== false){
            $this->setValue($cookieName, $this->getValue($cookieName), $timeout, $path, $domain, $secure, $httponly);
        }
        return $this;
    }


    public function deleteCookie($cookieName, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {

        if (is_null($path)) {
            $path = $this->getPath();
        }
        if (is_null($domain)) {
            $domain = $this->getDomain();
        }
        if (is_null($secure)) {
            $secure = $this->isSecure();
        }
        if (is_null($httpOnly)) {
            $httpOnly = $this->getHttponly();
        }

        setcookie($cookieName, null, null, $path, $domain, $secure, $httpOnly);

        return $this;
    }
}

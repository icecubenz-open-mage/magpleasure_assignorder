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
class Magpleasure_Common_Helper_Http extends Mage_Core_Helper_Http
{
    protected $_httpXForwardedAddr;

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Retrieve Client X HTTP Forwarded Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getXForwardedAddr($ipToLong = false)
    {
        if (is_null($this->_httpXForwardedAddr)) {
            if (!$this->_httpXForwardedAddr) {
                $header = $this->_getRequest()->getServer('HTTP_X_FORWARDED_FOR');

                if ($header){
                    $set = array();
                    $ips = explode(",", $header);
                    foreach ($ips as $ip){
                        $this->_httpXForwardedAddr = trim($this->_commonHelper()->escapeHtml($ip));
                        break;
                    }
                }
            }
        }

        if (!$this->_httpXForwardedAddr) {
            return false;
        }

        return $ipToLong ? ip2long($this->_httpXForwardedAddr) : $this->_httpXForwardedAddr;
    }

}
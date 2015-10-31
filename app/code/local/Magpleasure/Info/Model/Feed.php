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
 * @package    Magpleasure_Info
 * @version    master
 * @copyright  Copyright (c) 2012-2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Info_Model_Feed extends Mage_Core_Model_Abstract
{
    const MAGPLEASURE_BASE_URL = 'https://www.magpleasure.com/';

    const DEFAULT_GET_CDN = 'version/feed/getCdn/';
    const DEFAULT_GET_FEED = 'version/feed/getFeed/';

    const CONFIG_PATH_FEED = 'mpinfo/config/feed';
    const CONFIG_PATH_CDN_URL = 'mpinfo/config/cdn_url';

    protected $_feed;

    /**
     * Helper
     *
     * @return Magpleasure_Info_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mpinfo');
    }

    protected function _cURLGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    protected function _getProductFromFeed($name)
    {
        $this->getFeed();
        foreach ($this->getFeed() as $item){
            if (isset($item['name']) && (strtolower($item['name']) == strtolower($name))){
                return new Varien_Object($item);
            }
        }
        return new Varien_Object();
    }

    public function getProductName($name)
    {
        return $this->_getProductFromFeed($name)->getProductName();
    }

    public function getAvailableVersion($name)
    {
        return $this->_getProductFromFeed($name)->getVersion();
    }

    public function getProductUrl($name)
    {
        return $this->_getProductFromFeed($name)->getProductUrl();
    }

    /**
     * Receive CDN Url
     *
     * @return Magpleasure_Info_Model_Feed
     */
    public function retrieveCdnUrl()
    {
        try {
            $result = $this->_cURLGet($this->getCdnUrl());
            if ($result){
                $data = false;

                try {
                    $data = Zend_Json::decode($result);
                } catch (Exception $e){

                }

                if ($data && isset($data['url']) && $data['url']){
                    $this->saveConfig(self::CONFIG_PATH_CDN_URL, $data['url']);
                }
            }
        } catch (Exception $e){
            $this->_helper()
                ->getCommonHelper()
                ->getException()
                ->logException($e)
                ;
        }
        return $this;
    }

    /**
     * Receive Feed
     *
     * @return Magpleasure_Info_Model_Feed
     */
    public function retrieveFeed()
    {
        try {
            $result = $this->_cURLGet($this->getFeedUrl());
            if ($result){

                $data = false;
                try {
                    $data = Zend_Json::decode($result);
                } catch (Exception $e){

                }

                if ($data && is_array($data)){
                    $this->saveConfig(self::CONFIG_PATH_FEED, $result);
                }
            }

        } catch (Exception $e){
            $this->_helper()
                ->getCommonHelper()
                ->getException()
                ->logException($e)
            ;
        }

        return $this;
    }

    /**
     * Feed Url
     *
     * @return mixed|string
     */
    public function getFeedUrl()
    {
        $url =  $this->getConfig(self::CONFIG_PATH_CDN_URL) ?
                $this->getConfig(self::CONFIG_PATH_CDN_URL) :
                self::MAGPLEASURE_BASE_URL . self::DEFAULT_GET_FEED;

        return $url;
    }

    /**
     * Retrieves Getting Feed Url
     *
     * @return string
     */
    public function getCdnUrl()
    {
        return self::MAGPLEASURE_BASE_URL.self::DEFAULT_GET_CDN;
    }

    /**
     * Get Feed
     *
     * @return array
     */
    public function getFeed()
    {
        if (!$this->_feed){

            $feed = array();

            try {
                $feed = $this->getConfig(self::CONFIG_PATH_FEED) ?
                    Zend_Json::decode($this->getConfig(self::CONFIG_PATH_FEED)) :
                    array();

            } catch (Exception $e){

            }

            $this->_feed = $feed;
        }
        return $this->_feed;
    }

    /**
     * Set Feed
     *
     * @param array $feed
     * @return Magpleasure_Info_Model_Feed
     */
    public function setFeed(array $feed)
    {
        return $this;
    }

    public function saveConfig($path, $value)
    {
        Mage::app()->getConfig()->saveConfig($path, $value);
        return $this;
    }

    public function getConfig($path)
    {
        return Mage::getStoreConfig($path);
    }
}



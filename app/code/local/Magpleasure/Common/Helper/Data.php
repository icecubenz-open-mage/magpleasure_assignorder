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

class Magpleasure_Common_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SEARCH_CORE = 'Magpleasure_Searchcore';

    /**
     * Array Transform Helper
     *
     * @return Magpleasure_Common_Helper_Arrays
     */
    public function getArrays()
    {
        return Mage::helper('magpleasure/arrays');
    }

    /**
     * Magento Core Helper
     *
     * @return Magpleasure_Common_Helper_Core
     */
    public function getCore()
    {
        return Mage::helper('magpleasure/core');
    }

    /**
     * SimpleDOM Library
     *
     * @return Magpleasure_Common_Helper_Simpledom
     */
    public function getSimpleDOM()
    {
        return Mage::helper('magpleasure/simpledom');
    }

    /**
     * Mobile Helper
     *
     * @return Magpleasure_Common_Helper_Mobile
     */
    public function getMobile()
    {
        return Mage::helper('magpleasure/mobile');
    }

    /**
     * Magento Helper
     *
     * @return Magpleasure_Common_Helper_Magento
     */
    public function getMagento()
    {
        return Mage::helper('magpleasure/magento');
    }

    /**
     * Exception Resolve Helper
     *
     * @return Magpleasure_Common_Helper_Exception
     */
    public function getException()
    {
        return Mage::helper('magpleasure/exception');
    }

    /**
     * Data Hash Helper
     *
     * @return Magpleasure_Common_Helper_Hash
     */
    public function getHash()
    {
        return Mage::helper('magpleasure/hash');
    }

    /**
     * Cache Helper
     *
     * @return Magpleasure_Common_Helper_Cache
     */
    public function getCache()
    {
        return Mage::helper('magpleasure/cache');
    }

    /**
     * Strings Process Helper
     *
     * @return Magpleasure_Common_Helper_Strings
     */
    public function getStrings()
    {
        return Mage::helper('magpleasure/strings');
    }

    /**
     * Database Actions Helper
     *
     * @return Magpleasure_Common_Helper_Database
     */
    public function getDatabase()
    {
        return Mage::helper('magpleasure/database');
    }

    /**
     * Files Helper
     *
     * @return Magpleasure_Common_Helper_Files
     */
    public function getFiles()
    {
        return Mage::helper('magpleasure/files');
    }

    /**
     * Widgets
     *
     * @return Magpleasure_Common_Helper_Widgets
     */
    public function getWidgets()
    {
        return Mage::helper('magpleasure/widgets');
    }


    /**
     * Request
     *
     * @return Magpleasure_Common_Helper_Request
     */
    public function getRequest()
    {
        return Mage::helper('magpleasure/request');
    }

    /**
     * Session Helper
     *
     * @return Magpleasure_Common_Helper_Session
     */
    public function getSession()
    {
        return Mage::helper('magpleasure/session');
    }

    /**
     * XML Helper
     *
     * @return Magpleasure_Common_Helper_Xml
     */
    public function getXml()
    {
        return Mage::helper('magpleasure/xml');
    }

    /**
     * EAV Helper
     *
     * @return Magpleasure_Common_Helper_Eav
     */
    public function getEav()
    {
        return Mage::helper('magpleasure/eav');
    }

    /**
     * CSV Helper
     *
     * @return Magpleasure_Common_Helper_Csv
     */
    public function getCsv()
    {
        return Mage::helper('magpleasure/csv');
    }

    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->getStrings()->escapeHtml($data, $allowedTags);
    }

    public function escapeUrl($data)
    {
        return $this->getStrings()->escapeUrl($data);
    }

    /**
     * Config Helper
     *
     * @return Magpleasure_Common_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper('magpleasure/config');
    }

    /**
     * Cookie Helper
     *
     * @return Magpleasure_Common_Helper_Cookie
     */
    public function getCookie()
    {
        return Mage::helper('magpleasure/cookie');
    }

    /**
     * Http Helper
     *
     * @return Magpleasure_Common_Helper_Http
     */
    public function getHttp()
    {
        return Mage::helper('magpleasure/http');
    }

    /**
     * Transliteration Helper
     *
     * @return Magpleasure_Common_Helper_Transliteration
     */
    public function getTransliteration()
    {
        return Mage::helper('magpleasure/transliteration');
    }

    public function isSearchCoreEnabled()
    {
        return $this->getMagento()->isModuleEnabled(self::SEARCH_CORE);
    }

    /**
     * Search Core Helper
     *
     * @return Magpleasure_Searchcore_Helper_Data
     */
    public function getSearchCore()
    {
        return $this->isSearchCoreEnabled() ? Mage::helper('searchcore') : false;
    }

    /**
     * Store Helper
     *
     * @return Magpleasure_Common_Helper_Store
     */
    public function getStore()
    {
        return Mage::helper('magpleasure/store');
    }

    /**
     * Image Helper
     *
     * @return Magpleasure_Common_Helper_Image
     */
    public function getImage()
    {
        return Mage::helper('magpleasure/image');
    }

    /**
     * Underscore Helper
     * Normalized version of https://github.com/brianhaveri/Underscore.php
     *
     * @source https://github.com/brianhaveri/Underscore.php
     * @return Magpleasure_Common_Helper_Underscore
     */
    public function getUnderscore()
    {
        return Mage::helper('magpleasure/underscore');
    }
}
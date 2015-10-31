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
class Magpleasure_Common_Helper_Cache extends Mage_Core_Helper_Abstract
{
    const MAGPLEASURE_CACHE_KEY = 'MAGPLEASURE_DATA';

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function resetCache()
    {
        if (Mage::app()->useCache('magpleasure')){
            Mage::app()->cleanCache(self::MAGPLEASURE_CACHE_KEY);
        }

        return $this;
    }

    /**
     * Get Cached Html
     *
     * @param string $key
     * @return string
     */
    public function getPreparedHtml($key)
    {
        if (Mage::app()->useCache('magpleasure')){
            if ($html = Mage::app()->loadCache($key)){
                return $html;
            }
        }

        return false;
    }

    /**
     * Save Cached Html
     *
     * @param $key
     * @param $content
     * @param int $timeout
     * @param array $additionalTags
     * @return $this
     */
    public function savePreparedHtml($key, $content, $timeout = 3600, $additionalTags = null)
    {
        if (Mage::app()->useCache('magpleasure')){

            $tags = array(self::MAGPLEASURE_CACHE_KEY);

            if ($additionalTags && is_array($additionalTags) && count($additionalTags)){
                $tags = array_merge($tags, $additionalTags);
            }

            Mage::app()->saveCache($content, $key, $tags, $timeout);
        }

        return $this;
    }

    /**
     * Get Cached Value
     *
     * @param string $key
     * @return mixed|null
     */
    public function getPreparedValue($key)
    {
        if (Mage::app()->useCache('magpleasure')){
            if ($value = Mage::app()->loadCache($key)){
                try {
                    $value = unserialize($value);
                } catch (Exception $e) {
                    $this->_commonHelper()->getException()->logException($e);
                    $value = null;
                }
                return $value;
            }
        }

        return null;
    }

    /**
     * Save Cached Value
     *
     * @param $key
     * @param $value
     * @param int $timeout
     * @param array $additionalTags
     * @return $this
     */
    public function savePreparedValue($key, $value, $timeout = 3600, $additionalTags = null)
    {
        if (Mage::app()->useCache('magpleasure')){
            try {
                $tags = array(self::MAGPLEASURE_CACHE_KEY);

                if ($additionalTags && is_array($additionalTags) && count($additionalTags)){
                    $tags = array_merge($tags, $additionalTags);
                }

                Mage::app()->saveCache(serialize($value), $key, $tags, $timeout);
            } catch (Exception $e){
                $this->_commonHelper()->getException()->logException($e);
            }
        }

        return $this;
    }

    public function clearCachedData($key)
    {
        if (Mage::app()->useCache('magpleasure')){
            Mage::app()->removeCache($key);
        }

        return $this;
    }

    public function cleanCachedData(array $tags)
    {
        if (Mage::app()->useCache('magpleasure')){
            Mage::app()->cleanCache($tags);
        }
        return $this;
    }
}
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

class Magpleasure_Info_Model_Observer
{
    public function predispatchPage()
    {
        # Check Extensions
        if ((time() - (int) Mage::app()->loadCache('mp_info_check')) > Mage::getStoreConfig('mpinfo/check/timeout')) {

            /** @var $info Magpleasure_Info_Model_Info */
            $info = Mage::getSingleton('mpinfo/info');
            $info->getExtensions(true);
            Mage::app()->saveCache(time(), 'mp_info_check');
        }

        # Get Feed Url
        if ((time() - (int) Mage::app()->loadCache('mp_info_feed_url')) > Mage::getStoreConfig('mpinfo/url/timeout')) {

            /** @var $feed Magpleasure_Info_Model_Feed */
            $feed = Mage::getSingleton('mpinfo/feed');
            $feed->retrieveCdnUrl();
            Mage::app()->saveCache(time(), 'mp_info_feed_url');
        }

        # Get Feed
        if ((time() - (int) Mage::app()->loadCache('mp_info_feed')) > Mage::getStoreConfig('mpinfo/feed/timeout')) {

            /** @var $feed Magpleasure_Info_Model_Feed */
            $feed = Mage::getSingleton('mpinfo/feed');
            $feed->retrieveFeed();
            Mage::app()->saveCache(time(), 'mp_info_feed');
        }
    }
}



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

/**
 * Mobile Helper
 */
class Magpleasure_Common_Helper_Mobile extends Mage_Core_Helper_Abstract
{
    /**
     * iPhone Client Response
     */
    const IPHONE_CLIENT = 'iPhone';

    /**
     * Android Client Response
     */
    const ANDROID_CLIENT = 'Android';

    /**
     * Blackberry Client Response
     */
    const BLACKBERRY_CLIENT = 'BlackBerry';

    protected function _checkUserAgent($targetPlatform)
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])){
            return (strpos($_SERVER['HTTP_USER_AGENT'], $targetPlatform) !== false);
        }
        return false;
    }

    public function isAndroid()
    {
        return $this->_checkUserAgent(self::ANDROID_CLIENT);
    }

    public function isiPhone()
    {
        return $this->_checkUserAgent(self::IPHONE_CLIENT);
    }

    public function isBlackBerry()
    {
        return $this->_checkUserAgent(self::BLACKBERRY_CLIENT);
    }

    public function isMobile()
    {
        return $this->isAndroid() || $this->isiPhone() || $this->isBlackBerry();
    }
}
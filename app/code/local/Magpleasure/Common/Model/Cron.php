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
 * Abstract Model
 */
class Magpleasure_Common_Model_Cron
{
    protected $_timeout = 1800;
    protected $_cacheKey = "MAGPLEASURE_COMMON_CRON_KEY";

    protected function initCron(){}
    public function publicRun($schedule){}

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function setCacheKey($key)
    {
        $this->_cacheKey = $key;
    }

    public function setTimeout($value)
    {
        $this->_timeout = $value;
    }

    public function run($schedule)
    {
        $this->initCron();
        try {
            if($this->checkLock($this->_cacheKey)){
                $this->publicRun($schedule);
                Mage::app()->removeCache($this->_cacheKey);
            } else {
                echo "Extension's cron job has been locked";
            }
        } catch(Exception $e) {
            Mage::logException($e);
        }
    }

    protected function checkLock($lockKey)
    {
        if($time = Mage::app()->loadCache($lockKey)){
            if((time() - $time) <= $this->_timeout){
                return false;
            }
        }
        Mage::app()->saveCache(time(), $lockKey, array(), $this->_timeout);
        return true;
    }



}
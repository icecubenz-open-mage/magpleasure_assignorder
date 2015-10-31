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
 * Abstract Model Observer
 */
class Magpleasure_Common_Model_Observer_Abstract
{
    protected $_lockName = 'magpleasure_common_lock';

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function setLockName($lockName)
    {
        $this->_lockName = $lockName;
        return $this;
    }

    public function getLockName()
    {
        return $this->_lockName;
    }

    public function lock()
    {
        Mage::register($this->getLockName(), true, true);
    }

    public function isLocked()
    {
        return !!Mage::registry($this->getLockName());
    }

    public function unlock()
    {
        if ($this->isLocked()){
            Mage::unregister($this->getLockName());
        }
    }

}

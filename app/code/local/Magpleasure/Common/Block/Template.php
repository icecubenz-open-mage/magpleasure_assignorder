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

class Magpleasure_Common_Block_Template extends Mage_Core_Block_Template
{
    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    public function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Escape html entities
     *
     * @param   mixed $data
     * @param   array $allowedTags
     * @return  string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->_commonHelper()->getStrings()->escapeHtml($data, $allowedTags);
    }

    /**
     * Escape html entities in url
     *
     * @param string $data
     * @return string
     */
    public function escapeUrl($data)
    {
        return $this->_commonHelper()->getCore()->escapeUrl($data);
    }
}
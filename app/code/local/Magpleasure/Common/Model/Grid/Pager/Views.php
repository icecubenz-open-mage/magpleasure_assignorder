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
 * @copyright  Copyright (c) 2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Common_Model_Grid_Pager_Views extends Magpleasure_Common_Model_System_Config_Source_Abstract
{
    protected $_views = array(20, 30, 50, 100, 200);

    protected function _helper()
    {
        return Mage::helper('common');
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();
        foreach ($this->_views as $key => $value) {
            $data[$value] = $value;
        }
        return $data;
    }

    public function toJson()
    {
        $arr = $this->toArray();
        $result = array();
        foreach ($arr as $key => $value) {
            $result[] = array('id' => $value, 'name' => $value);

        }
        return Zend_Json::encode($result);

    }


}
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

if (Mage::helper('magpleasure')->getMagento()->checkVersion('1.6.0.0')) {
    class Magpleasure_Common_Model_Eav_Attribute extends Mage_Eav_Model_Attribute
    {
        protected $_options;

        public function getOptions($storeId = null)
        {
            if (!$this->_options) {
                /** @var $options Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection */
                $options = Mage::getResourceModel('eav/entity_attribute_option_collection');
                $options
                    ->setStoreFilter($storeId ? Mage::app()->getStore()->getId() : $storeId)
                    ->setPositionOrder()
                    ->addFieldToFilter('attribute_id', $this->getAttributeId());
                $this->_options = $options;
            }
            return $this->_options;
        }
    }
} else {
    class Magpleasure_Common_Model_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
    {
        protected $_options;

        public function getOptions($storeId = null)
        {
            if (!$this->_options) {
                /** @var $options Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection */
                $options = Mage::getResourceModel('eav/entity_attribute_option_collection');
                $options
                    ->setStoreFilter($storeId ? Mage::app()->getStore()->getId() : $storeId)
                    ->setPositionOrder()
                    ->addFieldToFilter('attribute_id', $this->getAttributeId());
                $this->_options = $options;
            }
            return $this->_options;
        }
    }
}


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
class Magpleasure_Common_Helper_Eav extends Mage_Core_Helper_Abstract
{
    protected $_helper;

    /**
     * EAV Helper
     *
     * @return Magpleasure_Common_Model_Eav_Helper
     */
    protected function _getEavHelper()
    {
        if (!$this->_helper){
            $this->_helper = new Magpleasure_Common_Model_Eav_Helper('core_setup');
        }
        return $this->_helper;
    }


    public function getEntityTypeIdByCode($entityCode)
    {
        $setup = $this->_getEavHelper();
        return $setup->getEntityType($entityCode, 'entity_type_id');
    }

    public function getAttributesByEntityType($entityCode)
    {
        $entityTypeId = $this->getEntityTypeIdByCode($entityCode);

        if ($entityTypeId){
            return $this->_getEavHelper()->getAttributes($entityTypeId);
        }
        return array();
    }

    public function getEntityNameByModelName($modelName)
    {
        return $this->_getEavHelper()->getEntityTypeNameByModelName($modelName, 'entity_model');
    }

    public function getAttributeById($attributeId)
    {
        return $this->_getEavHelper()->getAttributeById($attributeId);
    }

    public function getAttributeByCode($entityTypeId, $attributeId)
    {
        return $this->_getEavHelper()->getAttributeById($this->_getEavHelper()->getAttributeId($entityTypeId, $attributeId));
    }
}
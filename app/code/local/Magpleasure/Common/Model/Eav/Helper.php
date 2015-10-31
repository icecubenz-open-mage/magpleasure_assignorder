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

class Magpleasure_Common_Model_Eav_Helper extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Retrieves list of attributes for Entity Type
     *
     * @param $entityTypeId
     * @return array
     */
    public function getAttributes($entityTypeId)
    {
        $additionalTable    = $this->getEntityType($entityTypeId, 'additional_attribute_table');
        $entityTypeId       = $this->getEntityTypeId($entityTypeId);

        $mainTable   = $this->getTable('eav/attribute');

        $additionalTable = $this->getTable($additionalTable);
        $bind = array(
            'entity_type_id'    => $entityTypeId
        );

        $select = $this->_conn->select()
            ->from(array('main' => $mainTable));

        if ($additionalTable){
            $select->join(
                array('additional' => $additionalTable),
                'main.attribute_id = additional.attribute_id');
        }

        $select->where('main.entity_type_id = :entity_type_id');
        return $this->_conn->fetchAll($select, $bind);
    }


    public function getEntityTypeIdForAttribute($attributeId)
    {
        $mainTable   = $this->getTable('eav/attribute');

        $bind = array(
            'attribute_id' => $attributeId
        );

        $select = $this->_conn->select()
            ->from(array('main' => $mainTable), "main.entity_type_id");

        $select->where('main.attribute_id = :attribute_id');
        return $this->_conn->fetchOne($select, $bind);
    }

    public function getAttributeById($attributeId, $asArray = false)
    {
        $additionalTable = false;
        $entityId = $this->getEntityTypeIdForAttribute($attributeId);
        if ($entityId){
            $entity = $this->getEntityType($entityId);
            if ($entity){
                $additionalTable = isset($entity['additional_attribute_table']) ? $entity['additional_attribute_table'] : false;
            }
        }

        $mainTable   = $this->getTable('eav/attribute');

        $bind = array(
            'attribute_id'    => $attributeId
        );

        $select = $this->_conn->select()
            ->from(array('main' => $mainTable));

        if ($additionalTable){
            $select->join(
                array('additional' => $this->getTable($additionalTable)),
                'main.attribute_id = additional.attribute_id');
        }

        $select->where('main.attribute_id = :attribute_id');

        foreach ($this->_conn->fetchAssoc($select, $bind) as $row){

            if ($asArray){
                return $row;
            } else {
                /** @var $model Magpleasure_Common_Model_Eav_Attribute */
                $model = new Magpleasure_Common_Model_Eav_Attribute($row);
                return $model;
            }
        }

        return array();
    }

    public function getEntityTypeNameByModelName($modelName)
    {
        return $this->getTableRow('eav/entity_type', 'entity_model', $modelName, 'entity_type_id');
    }
}
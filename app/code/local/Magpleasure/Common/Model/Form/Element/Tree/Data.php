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

/** Data Model for Abstract Tree Render */
class Magpleasure_Common_Model_Form_Element_Tree_Data extends Varien_Object
{
    const CHILDREN = 'children';

    protected $_model;
    protected $_parentField;
    protected $_keyField;
    protected $_sortField;
    protected $_labelField;
    protected $_rootId;
    protected $_includeFields = array();
    protected $_showRoot = false;

    protected $_values = array();

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _getCommonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function setValues($values)
    {
        $this->_values = is_array($values) ? $values : explode(",", $values);
        return $this;
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function setTreeConfig(array $data)
    {
        $this->_model = $data['model'];
        $this->_parentField = $data['parent_field'];
        $this->_keyField = $data['key_field'];
        $this->_sortField = $data['sort_field'];
        $this->_labelField = $data['label_field'];
        $this->_rootId = $data['root_id'];

        if (isset($data['include_fields']) && is_array($data['include_fields'])){
            $this->_includeFields = $data['include_fields'];
        }

        if (isset($data['show_root'])){
            $this->_showRoot = $data['show_root'];
        }

        return $this;
    }

    public function showRoot()
    {
        return $this->_showRoot;
    }

    public function getFirstElement()
    {
        $model = Mage::getModel($this->_model)->load($this->_rootId);
        if ($model->getId()){
            return $model;
        }
        return false;
    }

    public function getChildren(Mage_Core_Model_Abstract $object = null)
    {
        /** @var $collection Mage_Core_Model_Resource_Db_Collection_Abstract */
        $collection = Mage::getModel($this->_model)->getCollection();

        $collection
            ->addFieldToFilter($this->_parentField, $object ? $object->getId() : $this->_rootId)
            ->setOrder($this->_sortField)
            ;

        if (count($this->_includeFields)){
            if (method_exists($collection, 'addAttributeToSelect')){
                foreach ($this->_includeFields as $fieldName){
                    $collection->addAttributeToSelect($fieldName);
                }
            } else if (method_exists($collection, 'addFieldToSelect')) {
                foreach ($this->_includeFields as $fieldName){
                    $collection->addFieldToSelect($fieldName, $fieldName);
                }
            }
        }

        return $collection;
    }

    protected function _collectionToArray($collection)
    {
        $items = array();
        foreach ($collection as $object){
            $items[] = $this->_objectToArray($object);
        }
        return $items;
    }

    protected function _objectToArray(Mage_Core_Model_Abstract $object, $attachChildren = true)
    {
        $pattern = array(
            'node_id' => $this->_keyField,
            'id' => $this->_keyField,
            'text' => $this->_labelField,
            'parent_id' => $this->_parentField,
        );

        $item = array();

        foreach ($pattern as $key => $value){
            $item[$key] = $object->getData($value);
        }

        $isChecked = in_array($object->getData($this->_keyField), $this->_values);

        $item['checked'] = $isChecked;
        $item['expanded'] = $isChecked ? true : false;

        $item['allowDrag'] = false;
        $item['allowDrop'] = false;

        if ($attachChildren){

            $children = $this->getChildren($object);
            if ($children && count($children)){
                $item['children'] = $this->_collectionToArray($children);
            }
        }

        return $item;
    }

    public function getArray()
    {
        $data = array();

        if ($object = $this->getFirstElement()){
            $data[] = $this->_objectToArray($object);
        } else {
            $data = $this->_collectionToArray($this->getChildren());
        }

        return $data;
    }

    public function getRootArray()
    {
        if ($object = $this->getFirstElement()){
            return $this->_objectToArray($object, false);
        }
        return array();
    }

    public function getLeafsArray()
    {
        return $this->_collectionToArray($this->getChildren());
    }

}
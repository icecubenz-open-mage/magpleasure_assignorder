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
 * Abstract Resource Model
 */
class Magpleasure_Common_Model_Resource_Treeview_Abstract extends Magpleasure_Common_Model_Resource_Abstract
{
    protected $_parentIdField;
    protected $_positionField;

    public function initTree($parentIdField, $positionField)
    {
        $this->_parentIdField = $parentIdField;
        $this->_positionField = $positionField;

        return $this;
    }

    public function getParentIdField()
    {
        return $this->_parentIdField;
    }

    public function getPositionField()
    {
        return $this->_positionField;
    }

    /**
     * Has Children
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function hasChildren(Mage_Core_Model_Abstract $object)
    {
        return !!$this->getChildren($object)->getSize();
    }

    public function getChildren(Mage_Core_Model_Abstract $object)
    {
        /** @var Magpleasure_Common_Model_Resource_Treeview_Collection_Abstract $collection */
        $collection = $object->getCollection();

        $collection
            ->addFieldToFilter($this->getParentIdField(), $object->getData($this->getParentIdField()))
            ->setOrder($this->getPositionField(), 'ASC');
            ;

        return $collection;
    }
}
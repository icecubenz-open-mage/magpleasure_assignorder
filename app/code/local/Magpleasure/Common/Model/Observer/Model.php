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
class Magpleasure_Common_Model_Observer_Model
{
    const PATH_MAGE_MODELS = 'global/models';

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _getCommonHelper()
    {
        return Mage::helper('magpleasure');
    }

    protected function _hasResourceModel(Mage_Core_Model_Config_Element $node)
    {
        foreach ($node->children() as $child){
            /** @var $child Mage_Core_Model_Config_Element */
            if ($child->getName() == 'resourceModel'){
                return (string)$child->asArray();
            }
        }
        return false;
    }

    protected function _getEntities($resourceModel)
    {
        $result = array();
        $node = Mage::getConfig()->getNode(self::PATH_MAGE_MODELS."/{$resourceModel}/entities");
        if ($node && $node->hasChildren()){
            foreach ($node->children() as $entity){
                /** @var $entity Mage_Core_Model_Config_Element */

                if ($entity && $entity->hasChildren()){
                    foreach ($entity->children() as $child){
                        /** @var $child Mage_Core_Model_Config_Element */
                        if ($child->getName() == 'table'){
                            $table = (string)$child->asArray();
                            $result[$entity->getName()] = $table;
                        }
                    }
                }

            }
        }
        return $result;
    }

    protected function _getLinksForEntity($resourceModel, $entity)
    {
        $result = array();
        $links = Mage::getConfig()->getNode(self::PATH_MAGE_MODELS."/{$resourceModel}/entities/{$entity}/links");
        if ($links && $links->hasChildren()){
            foreach ($links->children() as $link){

                /** @var $link Mage_Core_Model_Config_Element */
                if ($link->hasChildren()){
                    $details = array();
                    foreach ($link->children() as $detail){
                        /** @var $detail Mage_Core_Model_Config_Element */
                        $details[$detail->getName()] = (string)$detail->asArray();
                    }
                    $result[] = $details;
                }

            }
        }
        return $result;
    }

    /**
     * Find links for some model
     *
     * @param string $modelName
     * @return array
     */
    protected function _findLinksFor($modelName)
    {
        $result = array();
        foreach (Mage::getConfig()->getNode(self::PATH_MAGE_MODELS)->children() as $node){
            /** @var $node Mage_Core_Model_Config_Element */
            if ($resourceModel = $this->_hasResourceModel($node)){
                $model = $node->getName();
                foreach ($this->_getEntities($resourceModel) as $entity=>$table){
                    foreach ($this->_getLinksForEntity($resourceModel, $entity) as $link){
                        if (@$link['model'] == $modelName){
                            $link['local_model'] = "{$model}/{$entity}";
                            $result[] = new Varien_Object($link);
                        }
                    }
                }
            }
        }
        return $result;
    }

    protected function _proceedLink(Mage_Core_Model_Abstract $object, Varien_Object $link)
    {
        try {
            $modelName = $link->getLocalModel();
            if ($modelName && $object){
                $model = Mage::getModel($modelName);
                if ($model){
                    /** @var $resourceModel Magpleasure_Common_Model_Resource_Abstract */
                    $resourceModel = $model->getResource();
                    if ($object && ($value = $object->getData($link->getRemote()))){
                        $resourceModel->deleteRowsByLink($link->getLocal(), $value);
                    }
                }
            }
        } catch (Exception $e){
            $this->_getCommonHelper()
                ->getException()
                ->logException($e)
            ;
        }

        return $this;
    }

    public function coreModelDelete($event)
    {
        /** @var $object Mage_Core_Model_Abstract */
        $object = $event->getObject();
        $resourceName = $object->getResourceName();
        try {
            $links = $this->_findLinksFor($resourceName);
            if (count($links)){
                foreach ($links as $link){
                    $this->_proceedLink($object, $link);
                }
            }
        } catch (Exception $e){
            $this->_getCommonHelper()
                ->getException()
                ->logException($e)
                ;
        }
    }

}

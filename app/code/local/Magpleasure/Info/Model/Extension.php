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
 * @package    Magpleasure_Info
 * @version    master
 * @copyright  Copyright (c) 2012-2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Info_Model_Extension extends Varien_Object
{
    const STATUS_OK	= 'ok';
    const STATUS_NEED_UPDATE = 'need-update';
    const STATUS_WRONG_EDITION = 'wrong-edition';

    const TRACK_URL = "%s?utm_source=dashboard&utm_medium=link&utm_campaign=product&utm_content=%s";

    /**
     * Helper
     *
     * @return Magpleasure_Info_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mpinfo');
    }

    public function getModuleLicense($moduleName)
    {
        if ($licenseKey = (string)Mage::getConfig()->getNode("modules/$moduleName/license")) {
            return $licenseKey;
        } else {
            return false;
        }
    }

    public function getModuleEdition($moduleName)
    {
        if ($edition = (string)Mage::getConfig()->getNode("modules/$moduleName/edition")) {
            return $edition;
        } else {
            return Magpleasure_Common_Helper_Magento::EDITION_COMMUNITY;
        }
    }

    /**
     * Feed Model
     *
     * @return Magpleasure_Info_Model_Feed
     */
    public function getFeed()
    {
        return Mage::getSingleton('mpinfo/feed');
    }

    public function load($name)
    {
        $this->setData(array(
            'status' => self::STATUS_OK,
            'name' => $name,
            'edition' => $this->getModuleEdition($name),
            'license' => $this->getModuleLicense($name),
            'version' => $this->_helper()->getCommonHelper()->getMagento()->getModuleVersion($name),
            'is_loaded' => !!$this->_helper()->getCommonHelper()->getMagento()->getModuleVersion($name),
        ));
        return $this;
    }

    public function getNeedUpdate()
    {
        return $this->getStatus() == self::STATUS_NEED_UPDATE;
    }

    public function getStatus()
    {
        if (!$this->getIsLoaded()){
            return self::STATUS_OK;
        }

        if (strtolower($this->getEdition()) != strtolower($this->_helper()->getCommonHelper()->getMagento()->getEdition())){
            return self::STATUS_WRONG_EDITION;
        } elseif (version_compare($this->getAvailableVersion(), $this->getVersion(), '>')) {
            return self::STATUS_NEED_UPDATE;
        } else {
            return self::STATUS_OK;
        }
    }

    public function getStatusLabel()
    {
        if ($status = $this->getStatus()){
            foreach ($this->getOptionArray() as $value=>$label){
                if ($value == $status){
                    return $label;
                }
            }
        }
        return false;
    }

    public function getProductUrl()
    {
        $url = $this->getFeed()->getProductUrl($this->getName());
        $name = $this->getProductName();
        return sprintf(self::TRACK_URL, $url, htmlspecialchars($name));
    }

    public function getProductName()
    {
        return $this->getFeed()->getProductName($this->getName());
    }

    public function getAvailableVersion()
    {
        return $this->getFeed()->getAvailableVersion($this->getName());
    }

    public function getOptionArray()
    {
        return array(
            self::STATUS_OK => $this->_helper()->__("OK"),
            self::STATUS_NEED_UPDATE => $this->_helper()->__("New Version Available"),
            self::STATUS_WRONG_EDITION => $this->_helper()->__("Can not use the extension on this Magento Edition"),
        );
    }

    /**
     * Disable Output of Module
     *
     * @return Magpleasure_Info_Model_Extension
     */
    public function disableOutput()
    {
        $collection = Mage::getModel('core/config_data')->getCollection();
        $name = $this->getName();
        $collection
            ->getSelect()
            ->where("path = ?", "advanced/modules_disable_output/{$name}");

        $count = 0;
        foreach ($collection as $row) {
            $count++;
            $row->setValue(1)->save();
        }

        if (!$count) {
            Mage::getModel('core/config_data')
                ->setPath("advanced/modules_disable_output/{$name}")
                ->setValue(1)
                ->save();
        }
        return $this;
    }

    public function checkEdition()
    {
        return $this->getStatus() != self::STATUS_WRONG_EDITION;
    }

}
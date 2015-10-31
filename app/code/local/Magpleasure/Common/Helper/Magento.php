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
class Magpleasure_Common_Helper_Magento extends Mage_Core_Helper_Abstract
{
    const EDITION_COMMUNITY = 'ce';
    const EDITION_PROFESSIONAL = 'pe';
    const EDITION_ENTERPRICE = 'ee';
    const EDITION_GO = 'go';

    const METRIC_PRO = 'pro';
    const METRIC_ENTERPRISE = 'enterprise';

    const SYSTEM_EDITION_COMMUNITY    = 'Community';
    const SYSTEM_EDITION_ENTERPRISE   = 'Enterprise';
    const SYSTEM_EDITION_PROFESSIONAL = 'Professional';
    const SYSTEM_EDITION_GO           = 'Go';

    protected $_editionMapping = array(
        self::SYSTEM_EDITION_COMMUNITY => self::EDITION_COMMUNITY,
        self::SYSTEM_EDITION_ENTERPRISE => self::EDITION_ENTERPRICE,
        self::SYSTEM_EDITION_PROFESSIONAL => self::EDITION_PROFESSIONAL,
        self::SYSTEM_EDITION_GO => self::EDITION_GO,
    );

    /**
     * Retrieves Magento Edition
     *
     * @return string
     */
    public function getEdition()
    {
        if (method_exists('Mage','getEdition')){
            $systemEdition = Mage::getEdition();
            if (isset($this->_editionMapping[$systemEdition])){
                return $this->_editionMapping[$systemEdition];
            }
        }
        return $this->getBehaviourEdition();
    }

    /**
     * Retrieves Magento Edition using system behavior
     *
     * @return string
     */
    public function getBehaviourEdition()
    {
        $pathToEnterpiseConfig = BP . str_replace("/", DS, "/app/code/core/Enterprise/Enterprise/etc/config.xml");
        if (file_exists($pathToEnterpiseConfig)){
            $xml = @simplexml_load_file($pathToEnterpiseConfig,'SimpleXMLElement', LIBXML_NOCDATA);
            $package = (string)$xml->default->design->package->name;
            $theme = (string)$xml->install->design->theme->default;
            $skin = (string)$xml->stores->admin->design->theme->skin;

            if (($theme == self::METRIC_ENTERPRISE) && $this->isModuleEnabled('Enterprise_Enterprise')){
                return self::EDITION_ENTERPRICE;
            } elseif ($theme == self::METRIC_PRO) {
                return self::EDITION_PROFESSIONAL;
            }
        }
        return self::EDITION_COMMUNITY;
    }

    /**
     * Retrieves Magento Edition
     *
     * @return string
     */
    public function getVersion()
    {
        return Mage::getVersion();
    }

    /**
     * Retrieves Any Module Version
     *
     * @param string $moduleName
     * @return string
     */
    public function getModuleVersion($moduleName)
    {
        if ($version = (string)Mage::getConfig()->getNode("modules/$moduleName/version")) {
            return $version;
        } else {
            return false;
        }

    }

    /**
     * Check Magento Version for More or Equal case
     *
     * @param string $version
     * @return bool
     */
    public function checkVersion($version)
    {
        return version_compare($this->getVersion(), $version, '>=');
    }

    /**
     * Check Any Extension Version for More or Equal case
     *
     * @param string $moduleName
     * @param string $version
     * @return bool
     */
    public function checkModuleVersion($moduleName, $version)
    {
        if ($moduleVersion = $this->getModuleVersion($moduleName)){
            return version_compare($moduleVersion, $version, '>=');
        } else {
            return false;
        }
    }

    /**
     * Is Community Edition
     *
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEdition() == self::EDITION_COMMUNITY;
    }

    /**
     * Is Professional Edition
     *
     * @return bool
     */
    public function isProfessionsl()
    {
        return $this->getEdition() == self::EDITION_PROFESSIONAL;
    }

    /**
     * Is Enterprise Edition
     *
     * @return bool
     */
    public function isEnteprise()
    {
        return $this->getEdition() == self::EDITION_ENTERPRICE;
    }

    /**
     * Is Go Edition
     *
     * @return bool
     */
    public function isGo()
    {
        return $this->getEdition() == self::EDITION_GO;
    }

    /**
     * Check is module exists and enabled in global config.
     *
     * @param string $moduleName the full module name, example Mage_Core
     * @return boolean
     */
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }
}
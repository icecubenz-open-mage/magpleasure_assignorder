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

class Magpleasure_Info_Block_Adminhtml_Wrapper extends Mage_Adminhtml_Block_Template
{
    const BASE_URL = "https://www.magpleasure.com/version/track/save/h/%s/";

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mpinfo/wrapper.phtml');
    }

    /**
     * Helper
     *
     * @return Magpleasure_Info_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mpinfo');
    }

    public function getExtensions()
    {
        /** @var $info Magpleasure_Info_Model_Info */
        $info = Mage::getSingleton('mpinfo/info');
        return $info->getExtensions();
    }

    public function getImageSrc()
    {
        $data = array(
            'domain' => Mage::getBaseUrl('web'),
            'edition' => $this->_helper()->getCommonHelper()->getMagento()->getEdition(),
        );
        $extensions = array();
        foreach ($this->getExtensions() as $extension){
            /** @var $extension Magpleasure_Info_Model_Extension */
            $name = $extension->getName();
            $extensions[] = array(
                'name' => $name,
                'license' => $extension->getModuleLicense($name),
            );
        }

        $data['extensions'] = $extensions;

        try {
            $transport = serialize($data);
            $transport = base64_encode($transport);
            return sprintf(self::BASE_URL, $transport);
        } catch (Exception $e){
            $this->_helper()->getCommonHelper()->getException()->logException($e);
        }

        return $this->getSkinUrl('mpinfo/images/tp.png');
    }

    public function getCuttedUrl()
    {
        $url = $this->getImageSrc();
        $line = "url({$url}) no-repeat scroll 0 3px transparent";
        $lines = $this->_helper()->getCommonHelper()->getStrings()->cutToPieces($line, 80);
        return "'".implode("'+\n'", $lines)."'";
    }

    public function getCanShow()
    {
        # Get Feed
        if ((time() - Mage::app()->loadCache('mp_info_track')) > Mage::getStoreConfig('mpinfo/track/timeout')) {
            Mage::app()->saveCache(time(), 'mp_info_track');
            return true;
        }
        return false;
    }
}

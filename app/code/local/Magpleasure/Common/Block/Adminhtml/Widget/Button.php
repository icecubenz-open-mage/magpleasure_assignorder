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

class Magpleasure_Common_Block_Adminhtml_Widget_Button extends Mage_Adminhtml_Block_Widget_Button
{
    const CACHE_PREFIX = 'mp_common_adm_button_';

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    protected function _getAngularKeys()
    {
        return array(
            'ng-click',
            'ng-show',
            'ng-hide',
            'ng-if',
            'ng-disabled',
            'ng-class',
            'ng-style',
        );
    }

    public function getCacheKey()
    {
        $data = array();
        $keys = array(
            'label',
            'title',
            'class',
            'style',
            'additional_attributes',
            'onclick',
            'module_name',
        );

        $keys = array_merge($keys, $this->_getAngularKeys());

        foreach ($keys as $key){
            if ($this->hasData($key)){
                $data[] = $this->getData($key);
            }
        }

        return self::CACHE_PREFIX.$this->_commonHelper()->getHash()->getFastMd5Hash($data);
    }

    /**
     * Apply Additional Attributes to the Button
     *
     * @return string
     */
    protected function _toHtml()
    {
        $cacheKey = $this->getCacheKey();

        if ($html = $this->_commonHelper()->getCache()->getPreparedHtml($cacheKey)){
            return $html;

        } else {

            $html = parent::_toHtml();
            $injectAttributes = array();

            if ($additionalAttributes = $this->getAdditionalAttributes()){

                $attributesStr = explode(" ", $additionalAttributes);
                foreach ($attributesStr as $attributeStr){

                    if ($attributeStr){

                        try {
                            $key = false;
                            $value = false;
                            list($key, $value) = explode("=", $attributeStr);

                            if ($key && $value){

                                $value = trim($value, "\"'");
                                $injectAttributes[$key] = $value;
                            }

                        } catch (Exception $e){
                            $this->_commonHelper()->getException()->logException($e);
                        }
                    }
                }
            }

            foreach ($this->_getAngularKeys() as $key) {
                if ($this->hasData($key)){
                    $injectAttributes[$key] = $this->getData($key);
                }
            }

            if (count($injectAttributes)){

                $dom = $this->_commonHelper()->getSimpleDOM()->str_get_dom($html);
                foreach ($dom->find("button") as $button){
                    foreach ($injectAttributes as $key=>$value){
                        $button->setAttribute($key, $value);
                    }
                }

                $html = $dom->__toString();
            }

            $this->_commonHelper()->getCache()->savePreparedHtml($cacheKey, $html);
            return $html;
        }
    }

}
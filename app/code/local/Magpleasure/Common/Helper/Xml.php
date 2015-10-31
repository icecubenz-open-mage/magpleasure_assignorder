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
class Magpleasure_Common_Helper_Xml extends Mage_Core_Helper_Abstract
{

    protected function _arrayToXml($data, &$xml)
    {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $this->_arrayToXml($value, $xml->addChild("$key"));
                } else{
                    $this->_arrayToXml($value, $xml);
                }
            } else {
                $xml->addChild("$key","$value");
            }
        }
    }

    /**
     * Convert parametric array to XML
     *
     * @param array $data
     * @return SimpleXMLElement
     */
    public function getParamArrayToXml(array $data)
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><config></config>");
        $this->_arrayToXml($data, $xml);
        return $xml;
    }
}
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

class Magpleasure_Common_Model_Form_Element_Product extends Varien_Data_Form_Element_Text
{
    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _getCommonHelper()
    {
        return Mage::helper('magpleasure');
    }

    protected function _getProductName($productId)
    {
        /** @var Mage_Catalog_Model_Product $cusotmer  */
        $product = Mage::getModel('catalog/product')->load($productId);
        return $product->getName();
    }

    /**
     * Retrives element html
     * @return string
     */
    public function getElementHtml()
    {
        $productId = $this->getValue();
        $html = "";
        if ($productId){
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit', array('id'=>$productId));
            $html .= "<a href=\"{$url}\" target=\"_blank\">".$this->_getProductName($productId)."</a>";
        } else {
            $html .= $this->_getCommonHelper()->__('Product not found');
        }
        return $html;
    }
}
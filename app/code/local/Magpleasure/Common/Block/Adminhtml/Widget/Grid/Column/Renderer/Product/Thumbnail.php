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

class Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Product_Thumbnail
    extends Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Abstract
{
    public function getImageUrl(Mage_Catalog_Model_Product $product)
    {
        $imageUrl = $this->_getImageHelper()->init($product, 'image')->__toString();
        return $imageUrl;
    }

    public function getThumbnailUrl(Mage_Catalog_Model_Product $product)
    {
        $imageUrl = $this->_getImageHelper()->init($product, 'image')->resize($this->getWidth(), $this->getHeight())->__toString();
        return $imageUrl;
    }

    /**
     * Return Catalog Product Image helper instance
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _getImageHelper()
    {
        return Mage::helper('catalog/image');
    }

    public function getWidth()
    {
        return Mage::getStoreConfig('magpleasure/thumbnail/width');
    }

    public function getHeight()
    {
        return Mage::getStoreConfig('magpleasure/thumbnail/height');
    }

    /**
     * Product
     *
     * @param $productId
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function _getProduct($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getId()){
            return $product;
        }
        return false;
    }

    public function render(Varien_Object $row)
    {
        $productId = $this->_getValue($row);
        if ($productId && ($product = $this->_getProduct($productId))){
            $height = $this->getHeight();
            $width = $this->getWidth();
            try {
                $url = $this->getImageUrl($product);
                $thumbnailUrl = $this->getThumbnailUrl($product);
                $productName = $this->htmlEscape($product->getName());
                return "
                <div class=\"mp-common-image\" style=\"width: {$width}px; height: {$height}px;\">
                    <a class=\"mp-common-image-link\" href=\"{$url}\" rel=\"lightbox[product_{$productId}]\" >
                        <img width=\"{$width}px\" height=\"{$height}px\" src=\"{$thumbnailUrl}\" alt=\"{$productName}\" />
                    </a>
                </div>
            ";
            } catch (Exception $e) {
                return "";
            }
        }
        return "";
    }



}
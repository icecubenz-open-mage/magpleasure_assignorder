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
 * Media Image Model
 */
class Magpleasure_Common_Model_Media_Image extends Mage_Catalog_Model_Product_Image
{
    const POSTION_TOP     = 'top';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_CENTER = 'center';

    /**
     * Crop position from top
     *
     * @var float
     */
    protected $_topRate = 0.5;

    /**
     * Crop position from bootom
     *
     * @var float
     */
    protected $_bottomRate = 0.5;

    protected $_isAdaptive = false;

    /**
     * Set if this image has adaptive resize
     *
     * @param $isAdaptive
     * @return $this
     */
    public function setIsAdaptive($isAdaptive)
    {
        $this->_isAdaptive = $isAdaptive;
        return $this;
    }

    /**
     * Set if this image has adaptive resize
     *
     * @return boolean
     */
    public function getIsAdaptive()
    {
        return $this->_isAdaptive;
    }

    /**
     * @see Varien_Image_Adapter_Abstract
     * @return Mage_Catalog_Model_Product_Image
     */
    public function resize()
    {
        parent::resize();
        return $this;
    }

    /**
     * Adaptive Resize
     *
     * @return Bolevar_AdaptiveResize_Model_Catalog_Product_Image
     */
    public function adaptiveResize()
    {
        if (is_null($this->getWidth())) {
            return $this;
        }

        if (is_null($this->getHeight())) {
            $this->setHeight($this->getWidth());
        }

        $processor = $this->getImageProcessor();

        $currentRatio = $processor->getOriginalWidth() / $processor->getOriginalHeight();
        $targetRatio = $this->getWidth() / $this->getHeight();

        if ($targetRatio > $currentRatio) {
            $processor->resize($this->getWidth(), null);
        } else {
            $processor->resize(null, $this->getHeight());
        }

        $diffWidth  = $processor->getOriginalWidth() - $this->getWidth();
        $diffHeight = $processor->getOriginalHeight() - $this->getHeight();

        $processor->crop(
            floor($diffHeight * $this->_topRate),
            floor($diffWidth / 2),
            ceil($diffWidth / 2),
            ceil($diffHeight * $this->_bottomRate)
        );

        return $this;
    }

    /**
     * Set crop position
     *
     * @param string $position top, bottom or center
     *
     * @return Bolevar_AdaptiveResize_Model_Catalog_Product_Image
     */
    public function setCropPosition($position)
    {
        switch ($position) {
            case self::POSTION_TOP:
                $this->_topRate    = 0;
                $this->_bottomRate = 1;
                break;
            case self::POSITION_BOTTOM:
                $this->_topRate    = 1;
                $this->_bottomRate = 0;
                break;
            default:
                $this->_topRate    = 0.5;
                $this->_bottomRate = 0.5;
        }
        return $this;
    }

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return $this|Mage_Catalog_Model_Product_Image
     * @throws Exception
     */
    public function setBaseFile($file)
    {
        $file = trim($file, "/");
        $file = "/".$file;
        $baseFile = Mage::getBaseDir('media').str_replace("/", DS, $file);

        if ((!$file) || (!file_exists($baseFile))) {
            throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
        }

        $this->_baseFile = $baseFile;

        # build new filename (most important params)
        $path = array(
            $this->_commonHelper()->getFiles()->getDirName($baseFile),
            'cache',
            Mage::app()->getStore()->getId()
        );

        if (empty($this->_height)){
            $this->_height = $this->_width;
        }

        if((!empty($this->_width)) || (!empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";

        // add misk params as a hash
        $miscParams = array(
            ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
            ($this->_keepFrame        ? '' : 'no')  . 'frame',
            ($this->_isAdaptive       ? 'yes' : 'no'),
            'quality' . $this->_quality,
            'angle' . $this->_angle,
        );

        if ($this->_isAdaptive){
            $miscParams['top_rate'] = $this->_topRate;
            $miscParams['bottom_rate'] = $this->_bottomRate;
        }

        $path[] = md5(implode('_', $miscParams));
        $baseFileName = $this->_commonHelper()->getFiles()->getBaseName($baseFile);

        # append prepared filename
        $this->_newFile = implode(DS, $path). DS . $baseFileName;

        return $this;
    }

}
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

/** Media Image Helper */
class Magpleasure_Common_Helper_Image extends Mage_Core_Helper_Abstract
{
    const POSTION_TOP     = 'top';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_CENTER = 'center';

    /**
     * Current model
     * @var Magpleasure_Common_Model_Media_Image
     */
    protected $_model;

    /**
     * Scheduled for resize image
     * @var bool
     */
    protected $_scheduleResize = false;

    /**
     * Scheduled for rotate image
     * @var bool
     */
    protected $_scheduleRotate = false;
    protected $_scheduleMaxSizeUse = false;

    /**
     * Angle
     * @var int
     */
    protected $_angle;

    /**
     * Image File
     * @var string
     */
    protected $_imageFile;

    /**
     * Crop position
     *
     * @var string
     */
    protected $_cropPosition;

    /**
     * Adpative resize flag
     *
     * @var bool
     */
    protected $_scheduleAdaptiveResize = false;


    /**
     * Helper
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Reset all previous data
     *
     * @return Magpleasure_Common_Helper_Image
     */
    protected function _reset()
    {
        $this->_model = null;
        $this->_scheduleResize = false;
        $this->_scheduleMaxSizeUse = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_scheduleAdaptiveResize = false;
        $this->_cropPosition = 0;
        $this->_imageFile = null;
        return $this;
    }

    /**
     * Set current Image model
     *
     * @param Magpleasure_Common_Model_Media_Image $model
     * @return Magpleasure_Common_Helper_Image
     */
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Get current Image model
     *
     * @return Magpleasure_Common_Model_Media_Image
     */
    protected function _getModel()
    {
        return $this->_model;
    }

    /**
     * Set Rotation Angle
     *
     * @param $angle
     * @return $this
     */
    protected function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    /**
     * Get Rotation Angle
     *
     * @return int
     */
    protected function getAngle()
    {
        return $this->_angle;
    }


    /**
     * Set Image file
     *
     * @param string $file
     * @return Mage_Catalog_Helper_Image
     */
    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    /**
     * Get Image file
     *
     * @return string
     */
    protected function getImageFile()
    {
        return $this->_imageFile;
    }

    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        return array(
            $this->getOriginalWidth(),
            $this->getOriginalHeight()
        );
    }

    /**
     * Adaptive resize method
     *
     * @param int      $width  image width
     * @param int|null $height image height
     *
     * @return \Bolevar_AdaptiveResize_Helper_Image
     */
    public function adaptiveResize($width, $height = null)
    {
        $this->_getModel()
            ->setWidth($width)
            ->setHeight((!is_null($height)) ? $height : $width)
            ->setKeepAspectRatio(true)
            ->setKeepFrame(false)
            ->setConstrainOnly(false);
        ;

        $this->_scheduleAdaptiveResize = true;

        return $this;
    }

    /**
     * Set crop bosition
     *
     * @param string $position top, bottom or center
     *
     * @return \Bolevar_AdaptiveResize_Helper_Image
     */
    public function setCropPosition($position)
    {
        $this->_cropPosition = $position;
        return $this;
    }


    /**
     * Rotate image into specified angle
     *
     * @param $angle
     * @return $this
     */
    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->_getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    /**
     * Schedule resize of the image.
     *
     * @param $width
     * @param null $height
     * @return $this
     */
    public function resize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->_getModel()->setQuality($quality);
        return $this;
    }

    /**
     * Keep aspect ratio.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @param $flag
     * @return $this
     */
    public function keepAspectRatio($flag)
    {
        $this->_getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * @param $flag
     * @param array $position
     * @return $this
     */
    public function keepFrame($flag, $position = array('center', 'middle'))
    {
        $this->_getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param $flag
     * @return $this
     */
    public function constrainOnly($flag)
    {
        $this->_getModel()->setConstrainOnly($flag);
        return $this;
    }

    public function init($imageFile)
    {
        $this->_reset();

        /** @var Magpleasure_Common_Model_Media_Image $mediaImageModel */
        $mediaImageModel = Mage::getModel('magpleasure/media_image');
        $this->_setModel($mediaImageModel);

        if ($imageFile) {
            $this->setImageFile($imageFile);
        }

        return $this;
    }


    /**
     * Return Image URL
     *
     * @return string
     */
    public function __toString()
    {
        $url = false;
        try {
            $model = $this->_getModel();

            if ($this->_scheduleAdaptiveResize){
                $model->setIsAdaptive(true);
            }

            if ($this->getImageFile()) {
                $model->setBaseFile($this->getImageFile());
            }

            if ($model->isCached()) {
                $url = $model->getUrl();
            } else {

                if ($this->_scheduleRotate) {
                    $model->rotate($this->getAngle());
                }

                if ($this->_scheduleMaxSizeUse){

                    if (($this->getOriginalHeight() > $model->getMaxWidth()) || ($this->getOriginalWidth() > $model->getMaxHeight())){
                        $this->resize($model->getMaxWidth(), $model->getMaxHeight());
                    }
                }

                if ($this->_scheduleResize) {
                    $model->resize();
                }

                if ($this->_scheduleAdaptiveResize) {
                    $model->adaptiveResize();
                }

                $url = $model->saveFile()->getUrl();
            }

        } catch (Exception $e) {

            $this->_commonHelper()->getException()->logException($e);
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }

    /**
     * Schedule resize of the image to max values if need that
     *
     * @param $maxWidth
     * @param null $maxHeight
     * @return $this
     */
    public function setMaxSize($maxWidth, $maxHeight = null)
    {
        if (!$maxHeight){
            $maxHeight = $maxWidth;
        }

        $this->_getModel()->setMaxWidth($maxWidth)->setMaxHeight($maxHeight);
        $this->_scheduleMaxSizeUse = true;

        return $this;
    }

    /**
     * Get Placeholder
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return 'images/catalog/product/placeholder/image.jpg';
    }
}
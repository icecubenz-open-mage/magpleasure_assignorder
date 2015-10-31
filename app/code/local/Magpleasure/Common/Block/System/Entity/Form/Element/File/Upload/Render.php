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

class Magpleasure_Common_Block_System_Entity_Form_Element_File_Upload_Render
    extends Magpleasure_Common_Block_System_Entity_Form_Element_Abstract
{
    protected $_isImage = false;

    /**
     * Path to element template
     */
    const TEMPLATE_PATH = 'magpleasure/system/config/form/element/file/upload.phtml';

    protected $_collectData = array(
        'max_size',
        'allowed',
        'dir',
        'url',
        'html_id',
        'name',
    );

    public function isImage()
    {
        return $this->_isImage;
    }

    public function setIsImage($value)
    {
        $this->_isImage = $value;
        return $this;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE_PATH);

        $this->getThumbnailUrl();
    }

    public function getName()
    {
        return $this->getData('name') ? $this->getData('name') : $this->getData('html_id');
    }

    public function isAjax()
    {
        return $this->_commonHelper()->getRequest()->isAjax();
    }

    public function getUploadUrl()
    {
        $data = array();
        foreach ($this->_collectData as $key){
            if ($this->hasData($key)){
                $data[$key] = $this->getData($key);
            }
        }

        $data['is_image'] = $this->isImage();

        $hash = $this->_commonHelper()->getHash()->getHash($data);
        return $this->getUrl('magpleasure_admin/adminhtml_fileupload/upload', array('h' => $hash));
    }

    public function hasFile()
    {
        return !!$this->getValue();
    }

    protected function _dirExtension($fileName)
    {
        return substr($fileName, 0, 1) . DS . substr($fileName, 1, 1);
    }

    protected function _urlExtension($fileName)
    {
        return substr($fileName, 0, 1) . "/" . substr($fileName, 1, 1) . "/";
    }

    public function getThumbnailUrl()
    {
        $value = $this->getValue();
        $dir = $this->getDir() ? $this->getDir() : "default";
        $fieldName = $this->getHtmlId()."_file";
        $fileName = $this->_commonHelper()->getFiles()->getBaseName($value);
        $filePath = str_replace(DS, "/", $dir)."/".$fieldName."/"."thumbnail"."/".$this->_urlExtension($fileName)."/".$fileName;
        return Mage::getBaseUrl('media').$filePath;
    }

    public function getFileUrl()
    {
        $value = $this->getValue();
        return Mage::getBaseUrl('media').trim($value, "/");
    }

    public function getAttributeName()
    {
        if(Mage::app()->getRequest()->getControllerName() == 'catalog_product_action_attribute') {
            return "attributes[{$this->getName()}]";
        } else {
            return $this->getName();
        }
    }

    protected function _getFileType()
    {
        return $this->_commonHelper()->getFiles()->getExtension($this->getValue());
    }

    protected function _imageDetected()
    {
        return in_array($this->_getFileType(), $this->_commonHelper()->getFiles()->getAllowedImageExtensions());
    }

    public function getConfigJson()
    {
        $savedData = array(
            'upload_url' => $this->getUploadUrl(),
            'html_id' => $this->getHtmlId(),
            'response_key' => $this->getHtmlId()."_file",
            'dir_separator' => DS,
            'has_file' => false,
            'has_thumbnail' => false,
            'is_required' => $this->getRequired() ? true : false,
            'attribute_name' => $this->getAttributeName(),
            'class' => $this->getClass(),
        );

        if ($this->hasFile()){
            $savedData['has_file'] = true;

            $savedData['file_type'] = $this->_getFileType();
            $savedData['url'] = $this->getFileUrl();
            $savedData['value'] = $this->getValue();

            if ($this->_imageDetected()){
                $savedData['thumbnail_url'] = $this->getThumbnailUrl();
                $savedData['has_thumbnail'] = true;
                $savedData['is_image'] = true;
            }
        }

        return Zend_Json::encode($savedData);
    }
}

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

require_once Mage::getBaseDir('lib') . DS . "FileUpload" . DS . "MpUploadHandler.php";
class Magpleasure_Common_Adminhtml_FileuploadController extends Magpleasure_Common_Controller_Adminhtml_Action
{
    const TEMP_DIR = 'tmp';


    protected function _getUploadUrl($folder, $field = null)
    {
        $url = Mage::getBaseUrl('media') . $folder . "/";
        if ($field) {
            $url .= $field . "/";
        }
        return $url;
    }

    protected function _validateFormKey()
    {
        if ($this->getRequest()->getActionName() == 'upload') {
            return true;
        } else {
            return parent::_validateFormKey();
        }
    }

    protected function _getUploadDir($folder, $field = null)
    {
        $url = Mage::getBaseDir('media') . DS . $folder;
        if ($field) {
            $url .= DS . $field;
        }
        return $url;
    }

    protected function _getFileValue($file, $folder, $field)
    {
        if ($file && strlen($file) >= 2){
            $ch1 = $file[0];
            $ch2 = $file[1];

            return sprintf("/%s/%s/%s/%s/%s", $folder, $field, $ch1, $ch2, $file);
        }
        return false;
    }

    protected function _getDefaultAllowedTypes($isImage = false)
    {
        if ($isImage){
            return implode("|", $this->_commonHelper()->getFiles()->getAllowedImageExtensions());
        } else {
            return implode("|", $this->_commonHelper()->getFiles()->getAllowedFileExtensions());
        }
    }

    public function uploadAction()
    {
        if ($hash = $this->getRequest()->getParam('h')) {
            $initData = $this->_commonHelper()->getHash()->getData($hash);
        } else {
            $initData = array();
        }

        if (isset($initData['allowed']) && $initData['allowed'] && is_array($initData['allowed'])) {
            $allowedTypes = implode("|", $initData['allowed']);
        } else {
            $allowedTypes = $this->_getDefaultAllowedTypes($initData['is_image']);
        }

        $allowedTypesPattern = sprintf('/(\.|\/)(%s)$/i', $allowedTypes);

        $postName = isset($initData['html_id']) ? $initData['html_id'] : "default";
        $fieldName = $postName . "_file";

        $folder = isset($initData['dir']) ? $initData['dir'] : "default";
        $uploadDir = $this->_getUploadDir($folder, $fieldName);

        $folder = isset($initData['url']) ? $initData['url'] : "default";
        $uploadUrl = $this->_getUploadUrl($folder, $fieldName);

        try {

            ob_start();
            $uploadHandler = new MpUploadHandler(array(
                'script_url' => $this->getRequest()->getRequestUri(),
                'upload_dir' => $uploadDir,
                'upload_url' => $uploadUrl,
                'param_name' => $postName,
                'accept_file_types' => $allowedTypesPattern,
            ));
            $json = ob_get_clean();

            $data = Zend_Json::decode($json);

            if (isset($data[$postName][0])){

                $data = $data[$postName][0];

                $value = $this->_getFileValue($data['name'], $folder, $fieldName);

                $data['value'] = $value;

                $data['file_type'] = $this->_commonHelper()->getFiles()->getExtension($value);
                $data['is_image'] = in_array($data['file_type'], $this->_commonHelper()->getFiles()->getAllowedImageExtensions());

                if (!isset($data['error'])){
                    $data['error'] = false;
                } else {

                    $keysToUnset = array("name", "size", "type", "value");
                    foreach ($keysToUnset as $key){
                        if (isset($data[$key])){
                            unset($data[$key]);
                        }
                    }

                    $data['error'] = $this->_commonHelper()->__($data["error"]);
                }

                $this->getResponse()->setBody(Zend_Json::encode($data));
            }

        } catch (Exception $e) {

            $this->_commonHelper()->getException()->logException($e);
        }
    }
}
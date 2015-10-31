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

class Magpleasure_Common_Model_Core_Email_Template extends Mage_Core_Model_Email_Template
{
    protected $_isFake = false;
    protected $_viewPort = true;
    protected $_registryContentKey = "send_content";
    protected $_registrySubjectKey = "send_subject";

    protected $_inlineCssFile;

    /**
     * @param $registrySubjectKey
     * @return $this``
     */
    public function setRegistrySubjectKey($registrySubjectKey)
    {
        $this->_registrySubjectKey = $registrySubjectKey;
        return $this;
    }

    /**
     * @param $registryContentKey
     * @return $this
     */
    public function setRegistryContentKey($registryContentKey)
    {
        $this->_registryContentKey = $registryContentKey;
        return $this;
    }

    /**
     * @param $viewPort
     * @return $this
     */
    public function setViewPort($viewPort)
    {
        $this->_viewPort = $viewPort;
        return $this;
    }

    /**
     * @param $isFake
     * @return $this
     */
    public function setIsFake($isFake)
    {
        $this->_isFake = $isFake;
        return $this;
    }

    /**
     * @return Magpleasure_Common_Model_Core_Email_Template_Filter
     */
    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {

            $this->_templateFilter = Mage::getModel('magpleasure/core_email_template_filter');
            $this->_templateFilter
                ->setUseAbsoluteLinks(
                    $this->getUseAbsoluteLinks()
                )
                ->setStoreId(
                    $this->getDesignConfig()->getStore()
                )
            ;
        }
        return $this->_templateFilter;
    }

    /**
     * Merge HTML and CSS and returns HTML that has CSS styles applied "inline" to the HTML tags. This is necessary
     * in order to support all email clients.
     *
     * @param $html
     * @return string
     */
    protected function _applyInlineCss($html)
    {
        try {

            $inlineCssFile = $this->getInlineCssFile();

            if (strlen($html) && $inlineCssFile) {

                $cssToInline = $this->_getCssFileContent($inlineCssFile);

                $cssToInline = $this->_applyReplacements($cssToInline);

                require_once Mage::getBaseDir("lib").DS."Email".DS."Pelago".DS."Emogrifier.php";
                $emogrifier = new Email_Pelago_Emogrifier();
                $emogrifier->setHtml($html);
                $emogrifier->setCss($cssToInline);
                $emogrifier->setParseInlineStyleTags(false);
                $processedHtml = $emogrifier->emogrify();

            } else {
                $processedHtml = $html;
            }
        } catch (Exception $e) {
            $processedHtml = '{CSS inlining error: ' . $e->getMessage() . '}' . PHP_EOL . $html;
        }
        return $processedHtml;
    }

    /**
     * Load CSS content from filesystem
     *
     * @param string $filename
     * @return string
     */
    protected function _getCssFileContent($filename)
    {
        $storeId = $this->getDesignConfig()->getStore();
        $area = $this->getDesignConfig()->getArea();
        $package = Mage::getDesign()->getPackageName();
        $theme = Mage::getDesign()->getTheme('skin');

        $filePath = Mage::getDesign()->getFilename(
            $filename,
            array(
                '_type' => 'skin',
                '_default' => false,
                '_store' => $storeId,
                '_area' => $area,
                '_package' => $package,
                '_theme' => $theme,
            )
        );

        if (is_readable($filePath)) {
            return (string) file_get_contents($filePath);
        } else {
            return "";
        }
    }

    protected function _applyReplacements($css)
    {
        $replacements = $this->getTemplateFilter()->getInlineCssReplacements();

        if ($replacements){

            foreach ($replacements as $params){
                $css = preg_replace($params[0], $params[1], $css);
            }
        }

        return $css;
    }


    public function getProcessedTemplate(array $variables = array())
    {
        $html = parent::getProcessedTemplate($variables);

        if (!Mage::helper('magpleasure/magento')->checkModuleVersion("Mage_Core", "1.6.0.6")){

            $this->setInlineCssFile(
                $this
                    ->getTemplateFilter()
                    ->getInlineCssFile()
            );

            $html = $this->_applyInlineCss($html);
        }

        if ($this->_viewPort){

            $viewPortMetaTag = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            if (strpos($html, "<head>") !== false){
                $html = str_replace("<head>", "<head>".$viewPortMetaTag, $html);
            } else {
                $html = str_replace("<html>", "<html><head>".$viewPortMetaTag."</head>", $html);
            }
        }

        # TODO Change in future version
        $html = str_replace('<head>', '<head xmlns=\"http://www.w3.org/1999/xhtml\">', $html);
        $html = str_replace(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">',
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            $html
        );

        return $html;
    }

    public function send($email, $name = null, array $variables = array())
    {
        if ($this->_isFake){

            $registryContentKey = $this->_registryContentKey;
            if (Mage::registry($registryContentKey)){
                Mage::unregister($registryContentKey);
            }

            $registrySubjectKey = $this->_registrySubjectKey;
            if (Mage::registry($registrySubjectKey)){
                Mage::unregister($registrySubjectKey);
            }

            $emails = array_values((array)$email);
            $names = is_array($name) ? $name : (array)$name;
            $names = array_values($names);
            foreach ($emails as $key => $email) {
                if (!isset($names[$key])) {
                    $names[$key] = substr($email, 0, strpos($email, '@'));
                }
            }

            $variables['email'] = reset($emails);
            $variables['name'] = reset($names);

            $this->setUseAbsoluteLinks(true);
            $content = $this->getProcessedTemplate($variables);
            $subject = $this->getProcessedTemplateSubject($variables);
            Mage::register($registryContentKey, $content, true);
            Mage::register($registrySubjectKey, $subject, true);
            return true;

        } else {

            return parent::send($email, $name, $variables);
        }
    }
}
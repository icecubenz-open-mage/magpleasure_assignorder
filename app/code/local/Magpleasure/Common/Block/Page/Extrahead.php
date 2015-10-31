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
class Magpleasure_Common_Block_Page_Extrahead extends Magpleasure_Common_Block_Template
{
    protected $_angularLoaded = false;
    protected $_scripts = array();
    protected $_directives = array();
    protected $_factories = array();
    protected $_filters = array();
    protected $_controllers = array();
    protected $_configs = array();
    protected $_relations = array();

    protected $_templates = array();

    protected $_customParseSymbols = false;
    protected $_startSymbol = "{{";
    protected $_endSymbol = "}}";

    /** jQuery Scripts */
    protected $_safeScripts = array();

    /**
     * Set Angular JS loaded
     *
     * @param $value
     * @return $this
     */
    public function setAngularLoaded($value)
    {
        $this->_angularLoaded = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAngularLoaded()
    {
        return $this->_angularLoaded;
    }

    public function addDirective($name, $class)
    {
        $this->_directives[$name] = $class;
    }

    public function addFactory($name, $class)
    {
        $this->_factories[$name] = $class;
    }

    public function addController($name, $class)
    {
        $this->_controllers[$name] = $class;
    }

    public function addFilter($name, $class)
    {
        $this->_filters[$name] = $class;
    }

    public function addConfig($name, $class)
    {
        $this->_configs[$name] = $class;
    }

    public function hasDirectives()
    {
        return !!count($this->_directives);
    }

    public function hasFactories()
    {
        return !!count($this->_factories);
    }

    public function hasControllers()
    {
        return !!count($this->_controllers);
    }

    public function hasFilters()
    {
        return !!count($this->_filters);
    }

    public function hasConfigs()
    {
        return !!count($this->_configs);
    }

    public function addExtraJs($script)
    {
        $baseJsUrl = Mage::getBaseUrl('js');
        $url = $baseJsUrl . $script;
        $hash = md5($url);

        if (!isset($this->_scripts[$hash])) {
            $this->_scripts[$hash] = $url;
        }

        return $this;
    }

    public function getExtraScripts()
    {
        return $this->_scripts;
    }

    public function hasExtraScripts()
    {
        return count($this->_scripts);
    }

    /**
     * Predefine Angular Template to be shown only once
     * Usually used to define templates for embedded or custom direcives
     * Please use it for static templates only.
     *
     * @param $name Name to be called in the directive 'templateUrl'
     * @param $path Path to PHTML file
     * @param null $block The render block name if necessary (core/template is default)
     */
    public function addTemplate($name, $path = null, $block = null)
    {
        $template = array(
            'name' => $name,
        );

        if ($path){
            $template['path'] = $path;
        }

        if ($block){
            $template['block'] = $block;
        }

        $this->_templates[] = $template;
    }

    public function hasTemplates()
    {
        return count($this->_templates);
    }

    public function getTemplates()
    {
        return $this->_templates;
    }

    public function getTemplateHtml(array $template)
    {

        $cacheKey = "mp_comm_template_".md5(implode("_", array_merge($template, array(Mage::app()->getStore()->getId()))));

        if ($html = $this->_commonHelper()->getCache()->getPreparedHtml($cacheKey)){

            return $html;

        } else {

            $name = @$template['name'];
            $path = @$template['path'];

            if ($name && $path){

                $blockType = isset($template['block']) ? $template['block'] : 'core/template';

                /** @var Mage_Core_Block_Template $blockInstance */
                $blockInstance = $this->getLayout()->createBlock($blockType);

                if ($blockInstance){

                    if ($path){
                        $blockInstance->setTemplate($path);
                    }

                    $html = '<script type="text/ng-template" id="'.$name.'">';
                    $html .= $blockInstance->toHtml();
                    $html .= '</script>';

                    $this->_commonHelper()->getCache()->savePreparedHtml($cacheKey, $html);

                    return $html;
                }
            }
        }

        return false;
    }

    /**
     * JSON encoded object
     *
     * @param array $entities
     * @return string
     */
    public function _getSafeJsonObject(array &$entities)
    {
        $json = '';
        foreach ($entities as $key => $value) {
            if ($json != '') {
                $json .= ',';
            }
            $json .= $key . ':' . $value;
        }
        return '{' . $json . '}';
    }

    /**
     * JSON encoded array
     *
     * @param array $entities
     * @return string
     */
    public function _getSafeJsonArray(array &$entities)
    {
        $json = '';
        if (count($entities)){
            $json = implode(",", $entities);
        }
        return '[' . $json . ']';
    }

    public function getDirectivesJson()
    {
        return $this->_getSafeJsonObject($this->_directives);
    }

    public function getFactoriesJson()
    {
        return $this->_getSafeJsonObject($this->_factories);
    }

    public function getFiltersJson()
    {
        return $this->_getSafeJsonObject($this->_filters);
    }

    public function getControllersJson()
    {
        return $this->_getSafeJsonObject($this->_controllers);
    }

    public function getConfigsJson()
    {
        return $this->_getSafeJsonArray($this->_configs);
    }

    public function addRelation($relation)
    {
        $this->_relations[] = $relation;
        return $this;
    }

    public function getRelationsJson()
    {
        return Zend_Json::encode($this->_relations);
    }

    public function setAngularParseSymbols($startSymbol, $endSymbol)
    {
        $this->_startSymbol = $startSymbol;
        $this->_endSymbol = $endSymbol;
        $this->_customParseSymbols = true;

        return $this;
    }

    public function isCustomParseSymbols()
    {
        return !!$this->_customParseSymbols;
    }

    public function getStartSymbol()
    {
        return $this->_startSymbol;
    }

    public function getEndSymbol()
    {
        return $this->_endSymbol;
    }

    public function addSafeJs($alias, $path)
    {
        $this->_safeScripts[$alias] = $path;
        return $this;
    }

    public function hasSafeScripts()
    {
        return !!count($this->_safeScripts);
    }

    public function getSafeScripts()
    {
        $preparedScripts = array();
        foreach ($this->_safeScripts as $key => $value){
            $preparedScripts[$key] = Mage::getBaseUrl('js').$value;
        }
        return $preparedScripts;
    }

    public function getAliasFilter($alias)
    {
        $parts = explode(".", $alias);
        if (count($parts) > 1){

            $filterParts = array();
            $safeAlias = $parts[0];
            $filterParts[] = sprintf("typeof %s == 'undefined'", $safeAlias);

            for ($i = 1; $i < count($parts); $i++){

                $safeAlias .= ".".$parts[$i];
                $filterParts[] = sprintf("typeof %s == 'undefined'", $safeAlias);
            }

            $filter = "(".implode(") || (", $filterParts).")";

        } else {
            $filter = sprintf("typeof %s == 'undefined'", $alias);
        }

        return $filter;
    }
}
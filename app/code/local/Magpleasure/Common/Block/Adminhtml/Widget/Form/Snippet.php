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

class Magpleasure_Common_Block_Adminhtml_Widget_Form_Snippet
    extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element which re-rendering
     *
     * @var Varien_Data_Form_Element_Fieldset
     */
    protected $_element;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("magpleasure/widget/form/snippet.phtml");
    }

    /**
     * Retrieve an element
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        $this->addData($element->getData());
        return $this->toHtml();
    }

    public function getSnippedCodeHtml()
    {
        $html = $this->getSnippetTemplate();
        $bind = $this->getBind();
        if ($bind && is_array($bind)){
            foreach ($bind as $key => $value){
                $html = preg_replace("/{{".$key."}}/", $value, $html);
            }
        }
        return htmlspecialchars($html);
    }
}



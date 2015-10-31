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

class Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_container;

    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Container
     *
     * @return Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form_Container
     */
    public function getContainer()
    {
        return $this->_container;
    }

    public function getAction()
    {
        return $this->getUrl($this->getPostUrl(), array($this->getPostParam() => $this->getRequest()->getParam($this->getPostParam())));
    }

    public function getHtmlId()
    {
        return $this->getContainer()->getHtmlId();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $covering = new Varien_Data_Form_Element_Hidden(array(
            'value' => $this->getContainer()->getCovering(),
            'name' => 'covering',
        ));

        $this->getForm()->addElement($covering);
        $covering->setId('covering');

        return $this;
    }

}
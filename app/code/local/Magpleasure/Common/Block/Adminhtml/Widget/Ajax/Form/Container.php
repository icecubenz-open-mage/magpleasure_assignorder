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

class Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_buttonsToDelete = array('back', 'reset', 'delete', 'save');

    protected $_formData = array();

    protected $_entityId;


    public function getCovering()
    {
        return $this->_commonHelper()->getHash()->getHash($this->getFormData());
    }

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function __construct()
    {
        parent::__construct();

        foreach ($this->_buttonsToDelete as $id){
            $this->_removeButton($id);
        }

        $ajaxVarName = Mage::registry('mp_ajax_var_name');

        $this->_addButton('save', array(
            'label'     => $this->_commonHelper()->__('Save'),
            'class'     => 'default',
            'onclick'   => $ajaxVarName.'.save(); return false;',
        ));

        $this->_addButton('cancel', array(
            'label'     => $this->_commonHelper()->__('Cancel'),
            'class'     => 'cancel',
            'onclick'   => $ajaxVarName.'.close(); return false;',
        ));

        $this->setTemplate('magpleasure/ajax/form/container.phtml');
    }

    /**
     * Buttons Array
     *
     * @return array
     */
    public function getButtons()
    {
        $buttons = array();
        foreach ($this->_buttons as $level){
            foreach ($level as $id=>$lButton){
                $buttons[$id] = $lButton;
            }
        }
        return $buttons;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $form = $this->getChild('form');

        if ($form){
            $form
                ->addData($this->getFormData() ? $this->getFormData() : array())
                ->setContainer($this)
                ;
        }
    }

    public function renderView()
    {
        $this->_prepareLayout();
        return parent::renderView();
    }

    public function onSave($id = null, array $data)
    {
        # Put saving code here...
        return $this;
    }

    public function onLoad($id = null)
    {
        # Put loading code here...
        return $this;
    }

    public function getFormId()
    {
        return $this->getHtmlId()."Form";
    }

    public function setEntityId($entityId)
    {
        $this->_entityId = $entityId;
        return $this;
    }

    public function getEntityId()
    {
        return $this->_entityId;
    }

    public function getFormData()
    {
        return $this->_formData;
    }

    public function setFormData($formData)
    {
        $this->_formData = $formData;
        return $this;
    }
}
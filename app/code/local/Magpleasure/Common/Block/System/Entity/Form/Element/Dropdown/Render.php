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

class Magpleasure_Common_Block_System_Entity_Form_Element_Dropdown_Render extends Mage_Adminhtml_Block_Abstract
{
    /**
     * Path to element template
     */
    const TEMPLATE_PATH = 'magpleasure/system/config/form/element/dropdown.phtml';

    protected function  _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE_PATH);

    }

    public function getName()
    {
        return $this->getData('name') ? $this->getData('name') : $this->getData('html_id');
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

    public function isAjax()
    {
        return $this->_commonHelper()->getRequest()->isAjax();
    }

    public function getParamsJson()
    {
        $params = array();

        if ($this->getFormatSelection()){
            $params[] = sprintf("formatSelection : %s", $this->getFormatSelection());
        }

        if ($this->getFormatResult()){
            $params[] = sprintf("formatResult : %s", $this->getFormatResult());
        }

        if ($this->getCanUseDefaultValue() || $this->getCanUseWebsiteValue()){
            $params[] = "disabled : true";
        }

        return "{".implode(",", $params)."}";
    }
}

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

class Magpleasure_Common_Block_System_Entity_Form_Element_File_Upload extends Varien_Data_Form_Element_Imagefile
{
    public function getRenderer()
    {
        $control = new Magpleasure_Common_Block_System_Entity_Form_Element_File_Upload_Render($this->getData());
        $control->setLayout(Mage::app()->getLayout());

        if (Mage::registry('current_product')){
            $control->setData('name', 'product['.$control->getName().']');
        }

        return $control;
    }


    public function getElementHtml()
    {
        $html = '';
        $html .= $this->getRenderer()->toHtml();
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}
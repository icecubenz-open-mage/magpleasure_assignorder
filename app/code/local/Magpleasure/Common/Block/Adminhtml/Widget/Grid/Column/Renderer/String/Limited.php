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

/** Renderer of text with limitation of characters to output (without expand) */
class Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_String_Limited
    extends Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Abstract
{
    const DEFAULT_LIMIT = 200;

    protected function _limit()
    {
        return $this->getColumn()->getLimit() ? $this->getColumn()->getLimit() : self::DEFAULT_LIMIT;
    }

    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        $limit = $this->_limit();

        if ($value && $this->_commonHelper()->getStrings()->strlen($value) > $limit){
            $value = $this->_commonHelper()->getStrings()->htmlToText($value);
            $value = $this->_commonHelper()->getStrings()->strLimit($value, $limit);
            $value .= "...";
        }

        return $value;
    }
}
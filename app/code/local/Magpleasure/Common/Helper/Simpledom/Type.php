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

class Magpleasure_Common_Helper_Simpledom_Type
{
    const TYPE_ELEMENT = 1;
    const TYPE_COMMENT = 2;
    const TYPE_TEXT =    3;
    const TYPE_ENDTAG =  4;
    const TYPE_ROOT =    5;
    const TYPE_UNKNOWN = 6;
    const QUOTE_DOUBLE = 0;
    const QUOTE_SINGLE = 1;
    const QUOTE_NO =     3;
    const INFO_BEGIN =   0;
    const INFO_END =     1;
    const INFO_QUOTE =   2;
    const INFO_SPACE =   3;
    const INFO_TEXT =    4;
    const INFO_INNER =   5;
    const INFO_OUTER =   6;
    const INFO_ENDSPACE = 7;
}
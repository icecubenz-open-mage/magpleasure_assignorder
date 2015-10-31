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

/**
 * Abstract Option Model
 */
class Magpleasure_Common_Model_System_Config_Source_Sorttype
    extends Magpleasure_Common_Model_System_Config_Source_Abstract
{
    public function toArray()
    {
        return array(
            Varien_Db_Select::SQL_ASC => $this->_commonHelper()->__("Ascending"),
            Varien_Db_Select::SQL_DESC => $this->_commonHelper()->__("Descending"),
        );
    }

}
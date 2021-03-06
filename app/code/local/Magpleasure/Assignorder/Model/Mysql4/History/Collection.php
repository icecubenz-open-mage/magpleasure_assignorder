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
 * @package    Magpleasure_Assignorder
 * @version    master
 * @copyright  Copyright (c) 2012 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Assignorder_Model_Mysql4_History_Collection extends Magpleasure_Assignorder_Model_Mysql4_History_Abstract
{
    protected $_last = null;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('assignorder/history');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->getItems() as $item) {
            $this->_last = $item;
        }
    }

    /**
     * Last Item
     *
     * @return Magpleasure_Assignorder_Model_History
     */
    public function getLastItem()
    {
        if (!$this->isLoaded()) {
            $this->load();
        }
        return $this->_last;
    }


}

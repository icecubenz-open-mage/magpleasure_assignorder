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
class Magpleasure_Common_Helper_Store extends Mage_Core_Helper_Abstract
{
    protected $_actualStoreIds;

    /**
     * Retrieves Actual Store Ids
     *
     * @return array
     */
    public function getFrontendStoreIds()
    {
        if (!$this->_actualStoreIds){
            $storeIds = array();

            /** @var Mage_Core_Model_Store $stores */
            $stores = Mage::getModel('core/store')->getCollection();
            foreach ($stores as $store){
                if ($store->getId()){
                    $storeIds[] = $store->getId();
                }
            }

            $this->_actualStoreIds = $storeIds;
        }
        return $this->_actualStoreIds;
    }

    /**
     * Retrieves all available store Ids
     *
     * @return array
     */
    public function getAllStores()
    {
        $frontendStoreIds = $this->getFrontendStoreIds();
        $frontendStoreIds[] = '0';
        return $frontendStoreIds;
    }
}
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
 * @copyright  Copyright (c) 2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Common_Adminhtml_AjaxgridController extends Magpleasure_Common_Controller_Adminhtml_Action
{

    protected $_collection;


    /**'
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     *
     */

    protected function getCollection()
    {

        return null;

    }


    private function setOrder($data)
    {
        if (isset($data['order_by']) != '') {
            $order_by = Zend_Json::decode($data['order_by']);
            if ($order_by && is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    /*1 - ASC; 2 - DESC*/
                    $direction = 'ASC';
                    if ($value == 2) {
                        $direction = 'DESC';
                    }
                    $this->_collection->addOrder($key, $direction);
                }
            }
        }
    }

    private function getPramsNavigator()
    {
        $page = (int)$this->_collection->getCurPage();
        $pages = (int)$this->_collection->getPageSize();
        return array(
            'page' => $page,
            'pages' => (int)$this->_collection->getLastPageNumber(),
            'limit' => $pages,
            'total' => (int)$this->_collection->getSize(),
            'is_first' => $page <= 1,
            'is_last' => $page >= $pages,
        );

    }

    protected function afterSetData(&$data)
    {
        /*nothing*/
    }

    protected function beforeReadData()
    {
        /*nothing*/

    }

    private function setPageLimit($limit, $page, $all)
    {
        if ($all == false) {
            if (!$limit) {
                $limit = 20;
            }
            if (!$page) {
                $page = 1;
            }
            /*set page data*/
            $this->_collection
                ->setCurPage($page)
                ->setPageSize($limit);

        } else {
            $this->_collection->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
            $this->_collection->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);
            $this->_collection->setPageSize(false);
        }
    }


    public function refreshAction()
    {
        $data = array();
        $_data = $this->getRequest()->getPost();
        $this->_collection = $this->getCollection();
        $can_pager = (isset($_data['can_pager']) && $_data['can_pager'] == 'true');

        $this->setPageLimit((isset($_data['limit'])) ? $_data['limit'] : null,
            (isset($_data['page'])) ? $_data['page'] : null, !$can_pager);

        /*set order data*/
        $this->setOrder($_data);

        $this->_collection->load();

        $this->beforeReadData();
        $items = $this->_collection->getItems();
        $recNo = 0;
        foreach ($items as $item) {
            $data[] = $item->getData();
            $data[$recNo]['recordNumber'] = $recNo + 1;
            $this->afterSetData($data);
            $recNo += 1;
        }
        $result['data'] = $data;
        if ($can_pager == true) {
            $result['navigator'] = $this->getPramsNavigator();
        }

        return $this->_helper()->_ajaxResponse($this->getResponse(), $result);
    }


}
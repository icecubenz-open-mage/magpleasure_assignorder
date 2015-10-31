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

class Magpleasure_Common_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Customer Session
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton("customer/session");
    }

    /**
     * Response for Ajax Request
     *
     * @param array $result
     */
    protected function _ajaxResponse($result = array(), $responseCode = 200)
    {
        $this->getResponse()->setHttpResponseCode($responseCode);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Retrieves Messages Html
     *
     * @return string
     */
    protected function _getMessageBlockHtml()
    {
        return $this->getLayout()->getMessagesBlock()->addMessages(Mage::getSingleton('customer/session')->getMessages(true))->toHtml();
    }
}
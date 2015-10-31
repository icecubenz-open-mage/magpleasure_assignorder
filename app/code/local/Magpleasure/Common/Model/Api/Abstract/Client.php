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
class Magpleasure_Common_Model_Api_Abstract_Client
{

    /**
     * Make abstract post Call to Endpoint
     *
     * @param $contentType
     * @param $apiEndpoint
     * @param $postContent
     * @return bool|mixed
     */
    protected function _call($apiEndpoint, $postContent, $contentType)
    {
        $curlOptions = array(
            CURLOPT_POST         => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => array('Content-Type' => $contentType),
            CURLOPT_URL => $apiEndpoint,
            CURLOPT_POSTFIELDS => $postContent,
        );

        $ch = curl_init();
        try {


            curl_setopt_array( $ch, $curlOptions );
            $data = curl_exec( $ch );
            curl_close( $ch );
            return $data;

        } catch (Exception $e){

            curl_close($ch);
            return false;
        }
    }
}
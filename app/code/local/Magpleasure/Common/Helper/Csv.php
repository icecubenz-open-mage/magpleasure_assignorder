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
class Magpleasure_Common_Helper_Csv extends Mage_Core_Helper_Abstract
{
    /**
     * Collect All Possible Keys
     *
     * @param array $data
     * @return array
     */
    protected function _collectKeys(array &$data)
    {
        $keys = array();
        foreach ($data as $item){
            $keys = array_unique(array_merge($keys, array_keys($item)));
        }
        return $keys;
    }

    /**
     * Normalize ITEM with KEYS
     *
     * @param array $item
     * @param array $keys
     */
    protected function _normalize(array &$item, array &$keys)
    {
        foreach ($keys as $key){
            if (!isset($item[$key])){
                $item[$key] = null;
            }
        }
    }

    public function dataToCsv(array &$items, $delimiter = ',', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false)
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        $keys = $this->_collectKeys($items);

        $lines = array();
        $output = array();
        foreach ($keys as $key){
            # Enclose fields containing $delimiter, $enclosure or whitespace
            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $key ) ) {
                $output[] = $enclosure.str_replace($enclosure, $enclosure.$enclosure, $key).$enclosure;
            } else {
                $output[] = $key;
            }
        }


        $lines[] = implode($delimiter, $output);
        foreach ($items as $item) {
            $this->_normalize($item, $keys);

            $output = array();
            foreach ($keys as $key){
                if ($item[$key] === null && $nullToMysqlNull) {
                    $output[] = 'NULL';
                    continue;
                }

                # Enclose fields containing $delimiter, $enclosure or whitespace
                if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $item[$key] ) ) {
                    $output[] = $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item[$key]).$enclosure;
                } else {
                    $output[] = $item[$key];
                }
            }

            $lines[] = implode($delimiter, $output);
        }
        return implode( "\n", $lines );
    }

    public function collectionToCsv(Varien_Data_Collection_Db $collection, $arrRequiredFields = array())
    {
        if (!$collection->isLoaded()){
            $collection->load();
        }

        $data = $collection->toArray($arrRequiredFields);
        if ( !($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract)){
            $data = isset($data['items']) ? $data['items'] : array();
        }

        return $this->dataToCsv($data);
    }


}
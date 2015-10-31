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
class Magpleasure_Common_Helper_Transliteration extends Mage_Core_Helper_Abstract
{
    protected $_map;
    protected $_tail_bytes;

    protected function _transliterationReplace($ord, $unknownChar = '?', $langCode = 'en')
    {
        $dbMap = $this->_getMap()->getMap();
        $dbVariant = ($langCode != 'en') ? $this->_getVariant()->getVariant() : array();

        $this->_map = array();

        $bank = $ord >> 8;

        if (!isset($this->_map[$bank][$langCode])) {

            $path = sprintf('x%02x', $bank);

            if (isset($dbMap[$path])) {

                $base = $dbMap[$path];

                if ($langCode != 'en' && isset($variant[$langCode])) {

                    $variant = $dbVariant[$path];

                    # Merge in language specific mappings.
                    $this->_map[$bank][$langCode] = $variant[$langCode] + $base;
                } else {
                    $this->_map[$bank][$langCode] = $base;
                }
            } else {
                $this->_map[$bank][$langCode] = array();
            }
        }

        $ord = $ord & 255;

        return isset($this->_map[$bank][$langCode][$ord]) ? $this->_map[$bank][$langCode][$ord] : $unknownChar;
    }

    /**
     * Unicode Transliteration Database
     *
     * @return Magpleasure_Common_Helper_Transliteration_Map
     */
    protected function _getMap()
    {
        return Mage::helper('magpleasure/transliteration_map');
    }

    /**
     * Database of Transliteration Variants
     *
     * @return Magpleasure_Common_Helper_Transliteration_Variant
     */
    protected function _getVariant()
    {
        return Mage::helper('magpleasure/transliteration_variant');
    }

    public function transliterate($string, $unknownChar = '?', $sourceLangcode = 'en')
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $this->_tail_bytes;

        if (!isset($this->_tail_bytes)) {
            # Each UTF-8 head byte is followed by a certain number of tail bytes.
            $this->_tail_bytes = array();
            for ($n = 0; $n < 256; $n++) {
                if ($n < 0xc0) {
                    $remaining = 0;
                } elseif ($n < 0xe0) {
                    $remaining = 1;
                } elseif ($n < 0xf0) {
                    $remaining = 2;
                } elseif ($n < 0xf8) {
                    $remaining = 3;
                } elseif ($n < 0xfc) {
                    $remaining = 4;
                } elseif ($n < 0xfe) {
                    $remaining = 5;
                } else {
                    $remaining = 0;
                }
                $this->_tail_bytes[chr($n)] = $remaining;
            }
        }

        preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

        $result = '';
        foreach ($matches[0] as $str) {
            if ($str[0] < "\x80") {
                $result .= $str;
                continue;
            }

            $head = '';
            $chunk = strlen($str);

            $len = $chunk + 1;

            for ($i = -1; --$len;) {
                $c = $str[++$i];
                if ($remaining = $this->_tail_bytes[$c]) {

                    $sequence = $head = $c;
                    do {

                        if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {

                            $sequence .= $c;

                        } else {
                            if ($len == 0) {

                                $result .= $unknownChar;
                                break 2;
                            } else {

                                $result .= $unknownChar;

                                --$i;
                                ++$len;
                                continue 2;
                            }
                        }
                    } while (--$remaining);

                    $n = ord($head);
                    if ($n <= 0xdf) {
                        $ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
                    } elseif ($n <= 0xef) {
                        $ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
                    } elseif ($n <= 0xf7) {
                        $ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 + (ord($sequence[2]) - 128) * 64 + (ord($sequence[3]) - 128);
                    } elseif ($n <= 0xfb) {
                        $ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 + (ord($sequence[2]) - 128) * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
                    } elseif ($n <= 0xfd) {
                        $ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 + (ord($sequence[2]) - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 + (ord($sequence[4]) - 128) * 64 + (ord($sequence[5]) - 128);
                    }

                    $result .= $this->_transliterationReplace($ord, $unknownChar, $sourceLangcode);

                    $head = '';
                } elseif ($c < "\x80") {
                    # ASCII byte.
                    $result .= $c;
                    $head = '';
                } elseif ($c < "\xc0") {
                    # Illegal tail bytes.
                    if ($head == '') {
                        $result .= $unknownChar;
                    }
                } else {
                    # Miscellaneous freaks.
                    $result .= $unknownChar;
                    $head = '';
                }
            }
        }
        return $result;
    }
}
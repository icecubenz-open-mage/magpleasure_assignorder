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
class Magpleasure_Common_Helper_Strings extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    protected $_defaultEncoding = "UTF-8";

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * @param $content
     *
     * @return string
     */
    protected function _cutBadSuffix($content)
    {
        $contentPieces = explode(" ", $content);
        if (count($contentPieces) > 1) {
            unset($contentPieces[count($contentPieces) - 1]);
        }
        $content = implode(" ", $contentPieces);

        return $content;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function strtoupper($value)
    {
        return function_exists("mb_strtoupper") ? mb_strtoupper($value, $this->_defaultEncoding) : strtoupper($value);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function strtolower($value)
    {
        return function_exists("mb_strtolower") ? mb_strtolower($value, $this->_defaultEncoding) : strtolower($value);
    }

    /**
     * @param $value
     *
     * @return int
     */
    public function strlen($value)
    {
        return function_exists("mb_strlen") ? mb_strlen($value, $this->_defaultEncoding) : strlen($value);
    }

    /**
     * @param      $haystack
     * @param      $needle
     * @param null $offset
     *
     * @return int
     */
    public function strpos($haystack, $needle, $offset = null)
    {
        return function_exists("mb_strpos") ? mb_strpos($haystack, $needle, $offset, $this->_defaultEncoding) : strpos($haystack, $needle, $offset);
    }

    /**
     * @param      $string
     * @param      $start
     * @param null $length
     *
     * @return string
     */
    public function substr($string, $start, $length = null)
    {
        return function_exists("mb_substr") ? mb_substr($string, $start, $length, $this->_defaultEncoding) : substr($string, $start, $length);
    }

    /**
     * @param $pattern
     * @param $replacement
     * @param $string
     *
     * @return mixed|string
     */
    public function ereg_replace($pattern, $replacement, $string)
    {
        return function_exists("mb_ereg_replace") ?
            mb_ereg_replace($pattern, $replacement, $string, $this->_defaultEncoding) :
            preg_replace("/" . $pattern . "/", $replacement, $string);
    }

    /**
     * Extract keywords from text
     *
     * @param string $text
     * @param int    $limit
     *
     * @return array
     */
    public function getKeywords($text, $limit = 5)
    {
        Varien_Profiler::start("mp::common::strings::get_keywords");

        /** @var Magpleasure_Common_Model_Type_Dictionary_Keywords $keywords */
        $keywords = Mage::getSingleton('magpleasure/type_dictionary_keywords');
        $text = $this->htmlToText($text);
        $resultArray = $keywords->extractKeywords($text, 3);

        Varien_Profiler::stop("mp::common::strings::get_keywords");

        return $resultArray;
    }

    /**
     * Cut long text
     *
     * @param      $content
     * @param      $limit
     * @param bool $htmlToText
     *
     * @return string
     */
    public function strLimit($content, $limit, $htmlToText = true)
    {
        if ($htmlToText) {
            $content = $this->htmlToText($content);
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($content, 'UTF-8') > $limit) {
                $content = $this->_cutBadSuffix(mb_substr($content, 0, $limit - 1, 'UTF-8'));
            }
        } else {
            if (strlen($content) > $limit) {
                $content = $this->_cutBadSuffix(substr($content, 0, $limit - 1));
            }
        }

        return $content;
    }

    /**
     * HTML to text
     *
     * @param string $content
     *
     * @return string
     */
    public function htmlToText($content)
    {
        return $this->sanitize($content);
    }

    /**
     * HTML to text without new lines
     *
     * @param string $content
     *
     * @return string
     */
    public function htmlToPlainText($content)
    {
        $content = $this->htmlToText($content);
        $content = str_replace(array("\n", "\r"), ' ', $content);

        return $content;
    }

    /**
     * Wrapper for standard strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string $allowableTags
     * @param bool   $escape
     *
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $escape = false)
    {
        $result = strip_tags($data, $allowableTags);

        return $escape ? $this->escapeHtml($result, $allowableTags) : $result;
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function removePunctuation($string)
    {
        $string = preg_replace("/[[:punct:]]/u", " ", $string);
        $string = preg_replace('/\s\s+/u', ' ', $string);

        return $string;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function sanitize($string)
    {
        $string = strip_tags($string);
        $string = html_entity_decode($string);
        $string = urldecode($string);
        $string = trim($string);

        return $string;
    }

    /**
     * Escape html entities
     *
     * @param   mixed $data
     * @param   array $allowedTags
     *
     * @return  mixed
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item);
            }
        } else {
            // process single item
            if (strlen($data)) {
                if (is_array($allowedTags) and !empty($allowedTags)) {
                    $allowed = implode('|', $allowedTags);
                    $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
                    $result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
                    $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
                } else {
                    $result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
                }
            } else {
                $result = $data;
            }
        }

        return $result;
    }


    /**
     * Cut Line into pieces
     *
     * @param $line
     * @param $limit
     *
     * @return array
     */
    public function cutToPieces($line, $limit)
    {
        $lines = array();
        $strLen = $this->strlen($line);
        $count = 0;
        if ($strLen > $limit) {
            for ($i = 0; ($count * $limit) < $strLen; $i += $limit) {
                $lines[] = $this->substr($line, $i, $limit);
                $count++;
            }

            if ($count * $limit != $strLen) {
                $i += $limit;
                $lines[] = $this->substr($line, $i, ($strLen - ($limit * $count)));
            }
        } else {
            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * Escape html entities in url
     *
     * @param string $data
     *
     * @return string
     */
    public function escapeUrl($data)
    {
        return htmlspecialchars($data);
    }

    /**
     * Generate Slug
     *
     * @param string $title
     *
     * @return string
     */
    public function generateSlug($title)
    {
        $title = urldecode($title);
        $title = $this->_commonHelper()->getTransliteration()->transliterate($title);
        $title = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $title));

        return $title;
    }

    /**
     * Function for generating typos for word.
     *
     * @param $word
     *
     * @return array
     */
    public function generateTypos($word)
    {
        $result = array();

        # TODO implement more keyboard layouts (depends by locale)
        $layout = array(
            '1' => array('2', 'q'),
            '2' => array('1', 'q', 'w', '3'),
            '3' => array('2', 'w', 'e', '4'),
            '4' => array('3', 'e', 'r', '5'),
            '5' => array('4', 'r', 't', '6'),
            '6' => array('5', 't', 'y', '7'),
            '7' => array('6', 'y', 'u', '8'),
            '8' => array('7', 'u', 'i', '9'),
            '9' => array('8', 'i', 'o', '0'),
            'q' => array('1', '2', 'w', 'a'),
            'w' => array('q', 'd', 'a', 's', 'e', '3', '2'),
            'e' => array('w', 'f', 's', 'd', 'r', '4', '3'),
            'r' => array('e', 'd', 'f', 't', '5', '4'),
            't' => array('r', 'f', 'g', 'y', '6', '5'),
            'y' => array('t', 'g', 'h', 'u', '7', '6'),
            'u' => array('y', 'h', 'j', 'i', '8', '7'),
            'i' => array('u', 'j', 'k', 'o', '9', '8'),
            'o' => array('i', 'k', 'l', 'p', '0', '9'),
            'p' => array('o', 'l', '-', '0'),
            'a' => array('z', 's', 'w', 'q'),
            's' => array('a', 'z', 'x', 'd', 'e', 'w'),
            'd' => array('s', 'x', 'c', 'f', 'r', 'e'),
            'f' => array('d', 'c', 'v', 'g', 't', 'r'),
            'g' => array('f', 'v', 'b', 'h', 'y', 't'),
            'h' => array('g', 'b', 'n', 'j', 'u', 'y'),
            'j' => array('h', 'n', 'm', 'k', 'i', 'u'),
            'k' => array('j', 'm', 'l', 'o', 'i'),
            'l' => array('k', 'p', 'o'),
            'z' => array('x', 's', 'a'),
            'x' => array('z', 'c', 'd', 's'),
            'c' => array('x', 'v', 'f', 'd'),
            'v' => array('c', 'b', 'g', 'f'),
            'b' => array('v', 'n', 'h', 'g'),
            'n' => array('b', 'm', 'j', 'h'),
            'm' => array('n', 'k', 'j')
        );

        # proximity typos
        for ($i = 0; $i < strlen($word); $i++) {
            foreach ($layout as $a => $b) {
                if (substr($word, $i, 1) == $a) {
                    foreach ($b as $replacement) {
                        $result[] = substr_replace($word, $replacement, $i, 1);
                    }
                }
            }
        }

        # missed letters typos
        for ($i = 0; $i < strlen($word); $i++) {
            $result[] = substr_replace($word, "", $i, 1);
        }

        # double letters typos
        for ($i = 0; $i < strlen($word); $i++) {
            $result[] = substr_replace($word, substr($word, $i, 1), $i, 0);
        }

        # swap letters typos
        for ($i = 0; $i < strlen($word); $i++) {
            $result[] = substr_replace($word, strrev(substr($word, $i, 2)), $i, 2);
            $result[] = substr_replace($word, strrev(substr($word, 2, $i)), 2, $i);
        }

        # insert letters typos, based on layout
        for ($i = 0; $i < strlen($word); $i++) {
            foreach ($layout as $letter => $value) {
                $result[] = substr_replace($word, $letter, $i, 0);
            }
        }

        return (array_unique($result));
    }
}
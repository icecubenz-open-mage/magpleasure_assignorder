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

/** Keywords */
class Magpleasure_Common_Model_Type_Dictionary_Keywords
{
    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    protected function _keywordCountSort($first, $sec)
    {
        return $sec[1] - $first[1];
    }

    protected function _extractKeywords($str, $minWordLen = 3, $minWordOccurrences = 2, $asArray = false, $maxWords = 8, $restrict = false)
    {
        $stringHelper = $this->_commonHelper()->getStrings();

        $str = str_replace(array("?", "!", ";", "(", ")", ":", "[", "]"), " ", $str);
        $str = str_replace(array("\n", "\r", "  "), " ", $str);
        $str = $stringHelper->strtolower($str);


        $str = $stringHelper->ereg_replace('[^\w0-9 ]', ' ', $str);
        $str = trim($stringHelper->ereg_replace('\s+', ' ', $str));

        $words = explode(' ', $str);

        // Only compare to common words if $restrict is set to false
        // Tags are returned based on any word in text
        // If we don't restrict tag usage, we'll remove common words from array
        if ($restrict == false) {
            $commonWords = array(
                # en_US
                'i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www',
                # ru_RU
                'и','мне','я','мы','они','в','на','по','под','к','от','это','как','что','когда','зачем','он','оно'
            );
            
            $words = array_udiff($words, $commonWords, 'strcasecmp');
        }

        // Restrict Keywords based on values in the $allowedWords array
        // Use if you want to limit available tags
//        if ($restrict == true) {
//            $allowedWords = array('engine', 'boeing', 'electrical', 'pneumatic', 'ice');
//            $words = array_uintersect($words, $allowedWords, 'strcasecmp');
//        }

        $keywords = array();

        while (($c_word = array_shift($words)) !== null) {
            if ($stringHelper->strlen($c_word) < $minWordLen) continue;

            $c_word = $stringHelper->strtolower($c_word);
            if (array_key_exists($c_word, $keywords)) $keywords[$c_word][1]++;
            else $keywords[$c_word] = array($c_word, 1);
        }
        usort($keywords, array(&$this, '_keywordCountSort'));

        $final_keywords = array();
        foreach ($keywords as $keyword_det) {
            if ($keyword_det[1] < $minWordOccurrences) break;
            array_push($final_keywords, $keyword_det[0]);
        }
        $final_keywords = array_slice($final_keywords, 0, $maxWords);
        return $asArray ? $final_keywords : implode(', ', $final_keywords);
    }


    public function extractKeywords($text, $minWordLimit = 3)
    {
        return $this->_extractKeywords($text, $minWordLimit, 2, true);
    }
}
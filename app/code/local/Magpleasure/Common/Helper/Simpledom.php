<?php
/**
 *  PHP Simple HTML DOM Parser
 *
 *  Official site: http://sourceforge.net/projects/simplehtmldom/
 *  Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
 *  Contributions by:
 *      Yousuke Kumakura (Attribute filters)
 *      Vadim Voituk (Negative indexes supports of "find" method)
 *      Antcs (Constructor with automatically load contents either text or file/url)
 *  Redistributions of files must retain the above copyright notice.
 *
 *  @version: 1.11
 *  @author: S.C. Chen <me578022@gmail.com>
 *  @license The MIT License
 *
 */

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

class Magpleasure_Common_Helper_Simpledom extends Mage_Core_Helper_Abstract
{
    public function file_get_html() {
        $dom = new Magpleasure_Common_Helper_Simpledom_Dom;
        $args = func_get_args();
        $dom->load(call_user_func_array('file_get_contents', $args), true);
        return $dom;
    }

    // get html dom form string
    public function str_get_html($str, $lowercase=true) {
        $dom = new Magpleasure_Common_Helper_Simpledom_Dom;
        $dom->load($str, $lowercase);
        return $dom;
    }

    // dump html dom tree
    public function dump_html_tree($node, $show_attr=true, $deep=0) {
        $lead = str_repeat('    ', $deep);
        echo $lead.$node->tag;
        if ($show_attr && count($node->attr)>0) {
            echo '(';
            foreach($node->attr as $k=>$v)
                echo "[$k]=>\"".$node->$k.'", ';
            echo ')';
        }
        echo "\n";

        foreach($node->nodes as $c){
            $this->dump_html_tree($c, $show_attr, $deep+1);
        }
    }

    // get dom form file (deprecated)
    public function file_get_dom() {
        $dom = new Magpleasure_Common_Helper_Simpledom_Dom;
        $args = func_get_args();
        $dom->load(call_user_func_array('file_get_contents', $args), true);
        return $dom;
    }

    // get dom form string (deprecated)
    public function str_get_dom($str, $lowercase=true) {
        $dom = new Magpleasure_Common_Helper_Simpledom_Dom;
        $dom->load($str, $lowercase);
        return $dom;
    }

}

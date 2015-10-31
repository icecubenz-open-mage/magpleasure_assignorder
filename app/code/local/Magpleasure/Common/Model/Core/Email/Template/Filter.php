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

class Magpleasure_Common_Model_Core_Email_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{
    protected $_inlineCssFile;
    protected $_inlineCssReplacements = array();

    /**
     * CSS Replacements
     *
     * @return array
     */
    public function getInlineCssReplacements()
    {
        return $this->_inlineCssReplacements;
    }

    public function inlinecssDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (isset($params['file'])) {
            $this->setInlineCssFile($params['file']);
        }
        return '';
    }

    public function replacecssDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (isset($params['from']) && isset($params['to'])) {

            $from = $params['from'];
            $to = $params['to'];
            $to = $this->_getVariable($to);

            if ($from && $to){

                $this->_inlineCssReplacements[] = array(
                    $from,
                    $to
                );
            }
        }
        return '';
    }

    /**
     * @param $filename
     * @return $this
     */
    public function setInlineCssFile($filename)
    {
        $this->_inlineCssFile = $filename;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInlineCssFile()
    {
        return $this->_inlineCssFile;
    }
}
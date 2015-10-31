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
 * @package    Magpleasure_Info
 * @version    master
 * @copyright  Copyright (c) 2012-2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Info_Model_Info extends Mage_Core_Model_Abstract
{
    const MAGPLEASURE_PREFIX = 'Magpleasure_';

    protected $_extensions;

    protected $_hiddenExtensions = array(
        'Magpleasure_Common',
        'Magpleasure_Info',
        'Magpleasure_Searchcore',
        'Magpleasure_Richpindexer',
        'Magpleasure_Visitor',
        'Magpleasure_Dialog',
    );

    public function _construct()
    {
        parent::_construct();
        $this->_init('info/info');
    }

    public function getExtensions($checkEdition = false)
    {
        if (!$this->_extensions){
            $extensions = array();

            $data = Mage::getConfig()->getNode('modules');
            foreach ($data[0] as $node){
                $name = $node->getName();
                if ((strpos($name, self::MAGPLEASURE_PREFIX) !== false) && !in_array($name, $this->_hiddenExtensions)){
                    /** @var $extension Magpleasure_Info_Model_Extension */
                    $extension = Mage::getModel('mpinfo/extension')->load($name);
                    if ($extension->getIsLoaded()){
                        $extensions[] = $extension;

                        # Check Edition of Magento
                        if ($checkEdition){
                            if (!$extension->checkEdition()){
                                $extension->disableOutput();
                            }
                        }
                    }
                }
            }
            $this->_extensions = $extensions;
        }
        return $this->_extensions;
    }

}
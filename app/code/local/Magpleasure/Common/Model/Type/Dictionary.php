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

/** Basic Dictionary */
class Magpleasure_Common_Model_Type_Dictionary implements ArrayAccess
{
    const KEY = 'k';
    const VALUE = 'v';

    protected $_data = array();

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
     * If $key is empty, checks whether there's any data in the object
     * Otherwise checks if the specified attribute is set.
     *
     * @param string $key
     * @return boolean
     */
    public function hasData($key='')
    {
        if ($key){
            foreach ($this->_data as $item){
                if ($item[self::KEY] == $key){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Unset data from the object.
     *
     * $key can be a string only. Array will be ignored.
     *
     * @param string $key
     * @return Varien_Object
     */
    public function unsetData($key=null)
    {
        if ($key){

            if ($this->hasData($key)){
                Mage::throwException("Key '{$key}' is not exists in Dictionary.");
            }

            $removeIndex = false;
            foreach ($this->_data as $index=>$item){
                if ($item[self::KEY] == $key){
                    $removeIndex = $index;
                    break;
                }
            }

            if ($removeIndex !== false){
                unset($this->_data[$removeIndex]);
            }
        }
        return $this;
    }


    /**
     * Retrieves data from the object
     *
     * If $key is empty will return all the data as an array
     * Otherwise it will return value of the attribute specified by $key
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key='')
    {
        if ($key){
            foreach ($this->_data as $item){
                if ($item[self::KEY] == $key){
                    return $item[self::VALUE];
                }
            }
        }
        return null;
    }

    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return Varien_Object
     */
    public function setData($key, $value=null)
    {
        if ($key){
            if ($this->hasData($key)){
                foreach ($this->_data as $index => $item){
                    if ($item[self::KEY] == $key){
                        $this->_data[$index][self::VALUE] = $value;
                    }
                }
            } else {
                $this->_data[] = array(
                    self::KEY => $key,
                    self::VALUE => $value,
                );
            }
        }
        return $this;
    }

    /**
     * Add value
     *
     * @param $key
     * @param $value
     * @return Magpleasure_Common_Model_Type_Dictionary
     */
    public function add($key, $value)
    {
        if ($this->hasData($key)){
            Mage::throwException("The key '{$key}' already exists in Dictionary.");
        }

        $this->setData($key, $value);
        return $this;
    }

    /**
     *
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->getData($key);
    }

    public function set($key, $value)
    {
        return $this->setData($key, $value);
    }


    /**
     * Clear all data
     *
     * @return Magpleasure_Common_Model_Type_Dictionary
     */
    public function clear()
    {
        $this->_data = array();
        return $this;
    }

    /**
     * Contains key
     *
     * @param $key
     * @return bool
     */
    public function containsKey($key)
    {
        return in_array($key, $this->keys());
    }

    /**
     * Contains value
     *
     * @param $value
     * @return bool
     */
    public function containsValue($value)
    {
        return in_array($value, $this->values());
    }

    /**
     * Remove by Key
     *
     * @param $key
     */
    public function remove($key)
    {
        if ($this->hasData($key)){
            $this->unsetData($key);
        }
    }

    /**
     * Keys
     *
     * @return array
     */
    public function keys()
    {
        $keys = array();
        foreach ($this->_data as $item){
            $keys[] = $item[self::KEY];
        }
        return $keys;
    }

    /**
     * Values
     *
     * @return array
     */
    public function values()
    {
        $values = array();
        foreach ($this->_data as $item){
            $values[] = $item[self::VALUE];
        }
        return $values;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->hasData($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getData($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setData($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->unsetData($offset);
    }

    protected function _compareAsc(array $a, array$b)
    {
        if ($a[self::VALUE] == $b[self::VALUE]){
            return 0;
        }
        return $a[self::VALUE] > $b[self::VALUE] ? 1 : -1;
    }

    protected function _compareDesc(array $a, array$b)
    {
        if ($a[self::VALUE] == $b[self::VALUE]){
            return 0;
        }
        return $a[self::VALUE] < $b[self::VALUE] ? 1 : -1;
    }


    public function sort()
    {
        usort($this->_data, array(&$this, "_compareAsc"));
        return $this;
    }

    public function rsort()
    {
        usort($this->_data, array(&$this, "_compareDesc"));
        return $this;
    }
}
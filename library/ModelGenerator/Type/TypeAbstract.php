<?php
abstract class ModelGenerator_Type_TypeAbstract
{
    /**
     * 
     * @var boolean
     */
    protected $_native;
    /**
     * 
     * @var string
     */
    protected $_name;

    /**
     * 
     * @return bool
     */
    public function isNative()
    {
        return $this->_native;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * 
     * @return string 
     */
    public function __toString()
    {
        return $this->getName();
    }
}
<?php
class ModelGenerator_NamingStrategy_RemoveClassName implements 
ModelGenerator_NamingStrategy_NamingStrategyInterface
{
    /**
     * 
     * @var ModelGenerator_NamingStrategy_RemoveClassName
     */
    protected $_innerStrategy;

    /**
     * @return ModelGenerator_NamingStrategy_RemoveClassName
     */
    public function getInnerStrategy()
    {
        return $this->_innerStrategy;
    }

    /**
     * @param ModelGenerator_NamingStrategy_NamingStrategyInterface $innerStrategy
     * @return ModelGenerator_NamingStrategy_Default
     */
    public function setInnerStrategy(
        ModelGenerator_NamingStrategy_NamingStrategyInterface $innerStrategy
    )
    {
        $this->_innerStrategy = $innerStrategy;
        return $this;
    }

    /**
     * 
     * @param ModelGenerator_NamingStrategy_NamingStrategyInterface $innerStrategy
     */
    public function __construct(
        ModelGenerator_NamingStrategy_NamingStrategyInterface $innerStrategy
    )
    {
        $this->setInnerStrategy($innerStrategy);
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getClassName()
     */
    public function getClassName($tableName, $appNamespace)
    {
        return $this->getInnerStrategy()
                    ->getClassName($tableName, $appNamespace);
    }
    
    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getMapperClassName()
     */
    public function getMapperClassName($tableName, $appNamespace)
    {
        return $this->getInnerStrategy()
                    ->getMapperClassName($tableName, $appNamespace);
    }
    
    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getMapperClassName()
     */
    public function getDbTableClassName($tableName, $appNamespace)
    {
        return $this->getInnerStrategy()
                    ->getDbTableClassName($tableName, $appNamespace);
    }
    
    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getFileName()
     */
    public function getFileName($tableName)
    {
        return $this->getInnerStrategy()
                    ->getFileName($tableName);
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getPropertyName()
     */
    public function getPropertyName($fieldName, $tableName, $prefix = '_')
    {
        $fieldName = $this->_normalize($fieldName, $tableName);
        
        return $this->getInnerStrategy()
                    ->getPropertyName($fieldName, $tableName, $prefix);
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getPropertyGetterName()
     */
    public function getPropertyGetterName($fieldName, $tableName)
    {
        $fieldName = $this->_normalize($fieldName, $tableName);
        
        return $this->getInnerStrategy()
                    ->getPropertyGetterName($fieldName, $tableName);
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getPropertySetterName()
     */
    public function getPropertySetterName($fieldName, $tableName)
    {
        $fieldName = $this->_normalize($fieldName, $tableName);
        
        return $this->getInnerStrategy()
                    ->getPropertySetterName($fieldName, $tableName);
    }

    /**
     * 
     * @param string $string
     * @param string $tableName
     * @return string
     */
    protected function _normalize($string, $tableName)
    {
        return str_ireplace($tableName, '', $string);
    }
}
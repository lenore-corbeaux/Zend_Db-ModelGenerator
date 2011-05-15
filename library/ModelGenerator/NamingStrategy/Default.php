<?php
class ModelGenerator_NamingStrategy_Default implements 
ModelGenerator_NamingStrategy_NamingStrategyInterface
{
    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getClassName()
     */
    public function getClassName($tableName, $appNamespace)
    {
        $className = $appNamespace . '_Model_' . $this->_normalize($tableName);
        return $className;
    }
    
    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getMapperClassName()
     */
    public function getMapperClassName($tableName, $appNamespace)
    {
        $className = $appNamespace . '_Model_Mapper_'
                   . $this->_normalize($tableName);
                   
        return $className;
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getPropertyName()
     */
    public function getPropertyName($fieldName, $tableName, $prefix = '_')
    {
        return $prefix . lcfirst($this->_normalize($fieldName));
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getPropertyGetterName()
     */
    public function getPropertyGetterName($fieldName, $tableName)
    {
        return 'get' . $this->_normalize($fieldName);
    }

    /**
     * 
     * @see ModelGenerator_NamingStrategy_NamingStrategyInterface::getPropertySetterName()
     */
    public function getPropertySetterName($fieldName, $tableName)
    {
        return 'set' . $this->_normalize($fieldName);
    }

    /**
     * 
     * @param string $string
     */
    protected function _normalize($string)
    {
        $tabString = explode('_', $string);
        $finalString = array();
        
        foreach ($tabString as $word) {
            $finalString[] = ucfirst($word);
        }
        
        $finalString = implode('', $finalString);
        return $finalString;
    }
}
<?php
interface ModelGenerator_NamingStrategy_NamingStrategyInterface
{
    /**
     * 
     * @param string $tableName
     * @param string $appNamespace
     * @return string
     */
    public function getClassName($tableName, $appNamespace);

    /**
     * 
     * @param string $fieldName
     * @param string $tableName
     * @return string
     */
    public function getPropertyName($fieldName, $tableName);

    /**
     * 
     * @param string $fieldName
     * @param string $tableName
     * @return string
     */
    public function getPropertyGetterName($fieldName, $tableName);

    /**
     * 
     * @param string $fieldName
     * @param string $tableName
     * @return string
     */
    public function getPropertySetterName($fieldName, $tableName);
}
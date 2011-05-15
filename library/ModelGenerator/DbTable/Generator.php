<?php
class ModelGenerator_DbTable_Generator extends Zend_CodeGenerator_Php_Class
{    
    /**
     * 
     * @var string
     */
    protected $_tableName;
    
    /**
     * 
     * @var string
     */
    protected $_appNamespace = 'Application';
    
    /**
     * 
     * @var ModelGenerator_NamingStrategy_NamingStrategyInterface
     */
    protected $_namingStrategy;

    /**
     * 
     * @return string
     */
    public function getAppNamespace()
    {
        return $this->_appNamespace;
    }

    /**
     * 
     * @param string $appNamespace
     * @return ModelGenerator
     */
    public function setAppNamespace($appNamespace)
    {
        $this->_appNamespace = $appNamespace;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * 
     * @param string $tableName
     * @return ModelGenerator
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * 
     * @return ModelGenerator_NamingStrategy_NamingStrategyInterface
     */
    public function getNamingStrategy()
    {
        if (null === $this->_namingStrategy) {
            $this->_namingStrategy = new ModelGenerator_NamingStrategy_Default();
        }
        return $this->_namingStrategy;
    }

    /**
     * 
     * @param ModelGenerator_NamingStrategy_NamingStrategyInterface $namingStrategy
     * @return ModelGenerator_DomainModel_Generator
     */
    public function setNamingStrategy(
        ModelGenerator_NamingStrategy_NamingStrategyInterface $namingStrategy
    )
    {
        $this->_namingStrategy = $namingStrategy;
        return $this;
    }

    /**
     * 
     * @return string
     * @see Zend_CodeGenerator_Php_Class::generate()
     */
    public function generate()
    {
        $this->setExtendedClass('Zend_Db_Table_Abstract');
        $appNamespace = $this->getAppNamespace();
        $tableName = $this->getTableName();
        
        if (null !== $tableName) {
            $dbTableClassName = $this->_namingStrategy->getDbTableClassName(
                $tableName, 
                $appNamespace
            );
            
            $this->setName($dbTableClassName);
            
            $this->setDocblock(array(
            	'shortDescription' => "$tableName Table Data Gateway", 
                'tags' => array(
                    array('name' => 'category', 'description' => $appNamespace),
                    array('name' => 'package', 'description' => 'Default'), 
                    array('name' => 'subpackage', 'description' => 'Model')
                )
            ));
            
            $this->setProperty(array(
                'name' => '_name',
                'defaultValue' => $tableName
            ));
        }
             
        return parent::generate();
    }
}
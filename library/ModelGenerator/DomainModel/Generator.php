<?php
class ModelGenerator_DomainModel_Generator extends Zend_CodeGenerator_Php_Class
{
    /**
     * 
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    
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
     * @var ModelGenerator_Type_Factory_FactoryInterface
     */
    protected $_typeFactory;

    /**
     * 
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        return $this->_dbTable;
    }

    /**
     * 
     * @param Zend_Db_Table_Abstract $dbTable
     * @return Zend_Db_Table_Abstract
     */
    public function setDbTable($dbTable)
    {
        $this->_dbTable = $dbTable;
        return $this;
    }

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
     * @return ModelGenerator_Type_Factory_FactoryInterface
     */
    public function getTypeFactory()
    {
        if (null === $this->_typeFactory) {
            $this->_typeFactory = new ModelGenerator_Type_Factory_Default();
        }
        return $this->_typeFactory;
    }

    /**
     * 
     * @param ModelGenerator_Type_Factory_FactoryInterface $typeFactory
     * @return ModelGenerator_DomainModel_Generator
     */
    public function setTypeFactory(
        ModelGenerator_Type_Factory_FactoryInterface $typeFactory
    )
    {
        $this->_typeFactory = $typeFactory;
        return $this;
    }

    /**
     * 
     * @return string
     * @see Zend_CodeGenerator_Php_Class::generate()
     */
    public function generate()
    {
        $dbTable = $this->getDbTable();
        $appNamespace = $this->getAppNamespace();
        
        if (null !== $dbTable) {
            $tableInfos = $dbTable->info();
            $tableName = $tableInfos['name'];
            
            $className = $this->_namingStrategy->getClassName(
                $tableName, 
                $appNamespace
            );
            
            $this->setName($className);
            
            $this->setDocblock(array(
            	'shortDescription' => "$tableName Model", 
                'tags' => array(
                    array('name' => 'category', 'description' => $appNamespace), 
                    array('name' => 'package', 'description' => 'Default'), 
                    array('name' => 'subpackage', 'description' => 'Model')
                )
            ));
            
            foreach ($tableInfos['metadata'] as $name => $infos) {
                $this->addProperty(
                    $name,
                    $infos['DATA_TYPE'],
                    $tableName, 
                    $className
                );
            }
        }
        
        return parent::generate();
    }

    /**
     * 
     * @param string $fieldName
     * @param string $fieldType
     * @param string $tableName
     * @param string $className
     */
    public function addProperty($fieldName, $fieldType, $tableName, $className)
    {
        $type = $this->getTypeFactory()->factory($fieldType);
        $propertyName = $this->_namingStrategy->getPropertyName(
            $fieldName, 
            $tableName
        );
        
        $this->setProperty(array(
        	'docBlock' => array(
        		'tags' => array(
                    array('name' => 'var', 'description' => $type)
                )
            ), 
        	'visibility' => 'protected',
        	'name' => $propertyName
        ));
        
        $getterName = $this->_namingStrategy->getPropertyGetterName(
            $fieldName, 
            $tableName
        );
        
        $this->setMethod(array(
        	'docBlock' => array(
        		'tags' => array(
                    array(
                    	'name' => 'return',
                    	'description' => $type
                    )
                )
            ), 
        	'body' => 'return $this->' . $propertyName . ';', 
        	'visibility' => 'public', 'name' => $getterName
        ));
        
        $setterName = $this->_namingStrategy->getPropertySetterName(
            $fieldName, 
            $tableName
        );
        
        $setterBody = '$this->' . $propertyName . ' = '
                    . ($type->isNative() ? "($type) " : '')
                    . '$value;' . "\n"
                    . 'return $this;';
                    
        $this->setMethod(array(
        	'docBlock' => array(
        		'tags' => array(
                    array(
                    	'name' => 'param',
                    	'description' => $type . ' $value'
                    ), 
                    array(
                    	'name' => 'return',
                    	'description' => $className
                    )
                )
            ), 
        	'parameters' => array(array(
            	'name' => 'value', 
    			'type' => (!$type->isNative()) ? "$type " : null
            )),
    		'body' => $setterBody, 
    		'visibility' => 'public',
    		'name' => $setterName
        ));
    }
}
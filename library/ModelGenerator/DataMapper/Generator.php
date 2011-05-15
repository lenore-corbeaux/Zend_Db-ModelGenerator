<?php
class ModelGenerator_DataMapper_Generator extends Zend_CodeGenerator_Php_Class
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
            
            $mapperClassName = $this->_namingStrategy->getMapperClassName(
                $tableName, 
                $appNamespace
            );
            
            $modelClassName = $this->_namingStrategy->getClassName(
                $tableName, 
                $appNamespace
            );
            
            $this->setName($mapperClassName);
            
            $this->setDocblock(array(
            	'shortDescription' => "$tableName Data Mapper", 
                'tags' => array(
                    array('name' => 'category', 'description' => $appNamespace),
                    array('name' => 'package', 'description' => 'Default'), 
                    array('name' => 'subpackage', 'description' => 'Model')
                )
            ));
            
            $fields = array_keys($tableInfos['metadata']);
            
            $this->addConstructMethod()
                 ->addDbTableProperty($mapperClassName)
                 ->addCreateModelMethod($fields, $tableName, $modelClassName)
                 ->addCreateRowDataMethod($fields, $tableName, $modelClassName)
                 ->addFindMethod($modelClassName)
                 ->addSaveMethod(
                     $tableInfos, $modelClassName, $mapperClassName
                 );
        }
        
        return parent::generate();
    }
    
    /**
     * 
     * @return ModelGenerator_DataMapper_Generator
     */
    public function addConstructMethod()
    {
        $this->setMethod(array(
            'docBlock' => array(
                'longDescription' => 'Constructor : set Data Mapper',
                'tags' => array(array(
                    'name' => 'param',
                    'description' => 'Zend_Db_Table_Abstract $dbTable'
                ))
            ),
            'parameters' => array(array(
                'name' => 'dbTable',
                'type' => 'Zend_Db_Table_Abstract'
            )),
            'body' => '$this->setDbTable($dbTable);',
            'visibility' => 'public',
            'name' => '__construct'
        ));
        
        return $this;
    }
    
    /**
     * 
     * @param string $className
     * @return ModelGenerator_DataMapper_Generator
     */
    public function addDbTableProperty($className)
    {
        $this->setProperty(array(
            'docBlock' => array(
        		'tags' => array(
                    array(
                    	'name' => 'var',
                    	'description' => 'Zend_Db_Table_Abstract'
                    )
                )
            ),
            'name' => '_dbTable',
            'type' => 'Zend_Db_Table_Abstract'
        ));
        
        $this->setMethod(array(
            'docBlock' => array(
                'tags' => array(array(
                    'name' => 'return',
                    'description' => 'Zend_Db_Table_Abstract'
                ))
            ),
            'body' => 'return $this->_dbTable;',
            'visibility' => 'public',
            'name' => 'getDbTable'
        ));
             
        $this->setMethod(array(
            'docBlock' => array(
                'tags' => array(
                    array(
                        'name' => 'param',
                        'description' => 'Zend_Db_Table_Abstract $value'
                	),
                	array(
                	    'name' => 'return',
                        'description' => $className
                	)
                )
            ),
            'parameters' => array(array(
                'name' => 'value',
                'type' => 'Zend_Db_Table_Abstract'
            )), 
            'body' => '$this->_dbTable = $value;'
                      . "\n" . 'return $this;',
            'visibility' => 'public',
            'name' => 'setDbTable'
        ));
           
        return $this;
    }
    
    /**
     * 
     * @param array $fields
     * @param string $tableName
     * @param string $modelClassName
     * @return ModelGenerator_DataMapper_Generator
     */
    public function addCreateModelMethod(
        array $fields, $tableName, $modelClassName
    )
    {
        $namingStrategy = $this->getNamingStrategy();
        $tab = str_pad(' ', 4);
        $body = "return new $modelClassName(array(\n";
        
        foreach ($fields as $field) {
            $propertyName = $namingStrategy->getPropertyName(
                $field, $tableName, ''
            );
            
            $body .= "$tab'$propertyName' => \$data['$field'],\n";
        }
        
        $body .= "));";
        
        $this->setMethod(array(
            'docBlock' => array(
                'longDescription' => 'Turns an array from a query into a Model',
                'tags' => array(
                    array(
                        'name' => 'param',
                        'description' => 'array $data'
                	),
                	array(
                	    'name' => 'return',
                        'description' => $modelClassName
                	)
                )
            ),
            'parameters' => array(array(
                'name' => 'data',
                'type' => 'array'
            )), 
            'body' => $body,
            'visibility' => 'public',
            'name' => 'createModel'
        ));        
        
        return $this;
    }
    
    /**
     * 
     * @param array $fields
     * @param string $tableName
     * @param string $modelClassName
     * @return ModelGenerator_DataMapper_Generator
     */
    public function addCreateRowDataMethod(
        array $fields, $tableName, $modelClassName
    )
    {
        $namingStrategy = $this->getNamingStrategy();
        $tab = str_pad(' ', 4);
        $body = "return array(\n";
        
        foreach ($fields as $field) {
            $getterName = $namingStrategy->getPropertyGetterName(
                $field, $tableName
            );
            
            $body .= "$tab'$field' => \$model->$getterName(),\n";
        }
        
        $body .= ");";
        
        $this->setMethod(array(
            'docBlock' => array(
                'longDescription' => 'Generate a DbTable Row'
                					 . " from a model's data",
                'tags' => array(
                    array(
                        'name' => 'param',
                        'description' => 'array $data'
                	),
                	array(
                	    'name' => 'return',
                        'description' => 'array'
                	)
                )
            ),
            'parameters' => array(array(
                'name' => 'model',
                'type' => $modelClassName
            )), 
            'body' => $body,
            'visibility' => 'protected',
            'name' => '_createRowData'
        ));        
        
        return $this;
    }
    
    /**
     * 
     * @param string $modelClassName
     * @return ModelGenerator_DataMapper_Generator
     */
    public function addFindMethod($modelClassName)
    {
        $tab = str_pad(' ', 4);
        
        $body = <<< EOF
\$rowset = \$this->getDbTable()->find(\$primary);

if (!count(\$rowset)) {
${tab}return null;
}

return \$this->createModel(\$rowset->current()->toArray());
EOF;

        $this->setMethod(array(
            'docBlock' => array(
                'longDescription' => "Find a model by it's primary key",
                'tags' => array(
                    array(
                        'name' => 'param',
                        'description' => 'mixed $primary'
                	),
                	array(
                	    'name' => 'return',
                        'description' => "$modelClassName|null"
                	)
                )
            ),
            'parameters' => array(array(
                'name' => 'primary'
            )), 
            'body' => $body,
            'visibility' => 'public',
            'name' => 'find'
        ));
        
        return $this;
    }
    
    /**
     * 
     * @todo Handle composite primary keys
     * @param array $tableInfos
     * @param string $modelClassName
     * @param string $mapperClassName
     * @return ModelGenerator_DataMapper_Generator
     */
    public function addSaveMethod(
        $tableInfos, $modelClassName, $mapperClassName
    )
    {
        $tab = str_pad(' ', 4);
        $namingStrategy = $this->getNamingStrategy();
        $primary = $tableInfos['primary'][1];
        
        $primaryGetter = $namingStrategy->getPropertyGetterName(
            $primary, $tableInfos['name']
        );
        
        $body = <<< EOF
\$dbTable = \$this->getDbTable();
\$primary = \$model->$primaryGetter();

if (null !== \$primary) {
$tab\$row = \$dbTable->find(\$primary)
					 ->current();
} else {
$tab\$row = \$dbTable->createRow();
}

\$row->setFromArray(\$this->_createRowData(\$model))
    ->save();
     
\$newModel = \$this->createModel(\$row->toArray());
\$model->fromArray(\$newModel->toArray());

return \$this;
EOF;

        $this->setMethod(array(
            'docBlock' => array(
                'longDescription' => "Update or insert the given model",
                'tags' => array(
                    array(
                        'name' => 'param',
                        'description' => $modelClassName . ' $model'
                	),
                	array(
                	    'name' => 'return',
                        'description' => $mapperClassName
                	)
                )
            ),
            'parameters' => array(array(
                'name' => 'model',
                'type' => $modelClassName
            )), 
            'body' => $body,
            'visibility' => 'public',
            'name' => 'save'
        ));
        
        return $this;
    }
}
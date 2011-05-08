<?php
class ModelGenerator_Type_Factory_Default implements 
ModelGenerator_Type_Factory_FactoryInterface
{
    /**
     * 
     * @param string $fieldType
     * @return ModelGenerator_Type_TypeAbstract
     */
    public function factory($fieldType)
    {
        switch ($fieldType) {
            case 'int':
                return new ModelGenerator_Type_Integer();
                
            case 'varchar':
                return new ModelGenerator_Type_String();
                
            case 'date':
            case 'datetime':
            case 'timestamp':
                return new ModelGenerator_Type_Date();
                
            default:
                // Throws an exception here
                break;
        }
    }
}
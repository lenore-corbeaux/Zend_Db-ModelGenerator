<?php
interface ModelGenerator_Type_Factory_FactoryInterface
{
    /**
     * 
     * @param string $fieldType
     * @return ModelGenerator_Type_TypeAbstract
     */
    public function factory($fieldType);
}
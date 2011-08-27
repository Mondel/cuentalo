<?php

namespace Mondel\CuentaloBundle\Helpers;

class ObjectHelper
{
    
    /**
     * Completa los datos de un objeto, 
     * segun un array con sus valores.
     * 
     * @param object $object un nuevo objeto del tipo deseado
     * @param array $values con los valores del objeto
     * @return object $object completo
     */
    public static function getObject($object, array $data)
    {        
        foreach ($data as $propiedad => $valor) {
            $object->{'set'.ucfirst($propiedad)}($valor);
        }
        
        return $object;
    }
    
}
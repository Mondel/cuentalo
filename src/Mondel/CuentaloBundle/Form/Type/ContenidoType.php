<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContenidoType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('titulo')
                ->add('tipo', 'choice', array(
                    'choices'  => array('m' => 'Mensaje', 'a' => 'Anecdota', 's' => 'Secreto'),
                    'required' => true,
                ))
                ->add('texto');
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class'      => 'Mondel\CuentaloBundle\Entity\Contenido',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',            
            'intention'       => 'contenido_item',
        );
    }

    public function getName() {
        return 'contenido';
    }

}
<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContenidoType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('categoria')
                ->add('sexo', 'choice',
                        array(
                            'choices'       => array('m' => 'Masculino', 'f' => 'Femenino'),
                            'empty_value'   => 'Seleccione'
                        )
                )
                ->add('texto', null, array(
                    'max_length'        => 555,
                    'required'          => true,
                    'error_bubbling'    => true
                ));
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
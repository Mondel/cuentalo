<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContenidoType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $edades = array();
        for($i = 10; $i < 90; $i++) {
            $edades[$i - 10] = $i;
        }

        $builder->add('tipo', 'choice', array(
                    'choices'  => array('0' => 'Elegir', 'm' => 'Mensaje', 'a' => 'Anecdota', 's' => 'Secreto'),
                    'required' => true,
                ))
                ->add('sexo', 'choice', array(
                    'choices'   => array('m' => 'Masculino', 'f' => 'Femenino'),
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
<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UsuarioType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('nombre')
                ->add('apellido')
                ->add('email')
                ->add('contrasenia', 'repeated')
                ->add('contrasenia', 'repeated', array(
                    'type'            => 'password',
                    'invalid_message' => 'Las contraseñas no coinciden.',
                    'first_name'      => 'Contraseña',
                    'second_name'     => 'Repetir Contraseña'
                ))
                ->add('sexo', 'choice', array(
                    'choices'   => array('m' => 'Masculino', 'f' => 'Femenino'),
                    'required'  => true,
                ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class'      => 'Mondel\CuentaloBundle\Entity\Usuario',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',            
            'intention'       => 'usuario_item',
        );
    }

    public function getName() {
        return 'usuario';
    }

}
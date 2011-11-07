<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UsuarioType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder//->add('nombre')
                //->add('apellido')
                ->add('nick')
                // ->add('fecha_nacimiento', null, array('years' => range(1900,2000)))
                ->add('email')
                ->add('email_alternativo')
                //->add('contrasenia', 'password' , array('label' => 'Contraseña'))
                ->add('contrasenia', 'repeated', array(
                    'type'            => 'password',
                    'invalid_message' => 'Las contraseñas no coinciden.',
                    'first_name'      => 'contrasenia',
                    'second_name'     => 'repetirContrasenia',
                    'required'        => true,
                ))
                ->add('sexo', 'choice', array(
                    'choices'   => array('m' => 'Masculino', 'f' => 'Femenino'),
                    'required'  => true,
                    'empty_value' => 'Seleccione',
                ));
                //->add('recibe_noticias', null, array(
                //    'label'     => 'Quiere recibir noticias por email ?',
                //    'required'  => false
                //))
                //->add('recibe_notificaciones', null, array(
                //    'label'     => 'Quiere recibir notificaciones por email ?',
                //    'required'  => false
                //));
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
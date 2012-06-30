<?php

namespace Mondel\UserBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType,
	Symfony\Component\Form\CallbackValidator,
	Symfony\Component\Form\FormBuilderInterface,
	Symfony\Component\Form\FormInterface;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        	->add('name')
            ->add('last_name')
            ->add('nick')
            ->add('birth_date', 'birthday', array('years' => range(1900, 2000)))
            ->add('email')
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'Las contraseñas no coinciden.',
                'first_name'      => 'password',
                'second_name'     => 'repeatedPassword',
                'required'        => true,
            ))
            ->add('genre', 'choice', array(
                'choices'   => array('m' => 'Masculino', 'f' => 'Femenino', 'i' => 'Indefinido'),
                'required'  => true,
                'empty_value' => 'Seleccione',
            ))
            ->add('is_news_active', null, array(
                'label'     => 'Quiere recibir noticias por email ?',
                'required'  => false
            ))
            ->add('is_notifications_active', null, array(
                'label'     => 'Quiere recibir notificaciones por email ?',
                'required'  => false
            ))
            ->add('terms', 'checkbox', array(
        		'label' => 'Estoy de acuerdo con los terminos',
        		'property_path' => false,
            ));
        
		$builder->addValidator(
			new CallbackValidator(function(FormInterface $form)
			{
				if (!$form["terms"]->getData())
				{
					$form->addError(new FormError('Debe aceptar losTerminos y Condiciones'));
				}
			})
		);        
    }

    public function getName() {
        return 'mondel_userbundle_usertype';
    }

}
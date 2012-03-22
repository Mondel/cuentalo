<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MensajeType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('asunto', null, array())                
                ->add('usuarios_destinatarios', 'entity', array(
                    'multiple'      => true,
                    'required'      => false,
                    'empty_value'   => 'Destinatarios',
                    'property'      => 'nick',
                    'class'         => 'MondelCuentaloBundle:Usuario',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.nick', 'ASC');
                    },
                ))
                ->add('texto', null, array(                    
                    'required'          => true,
                    'error_bubbling'    => true
                ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class'      => 'Mondel\CuentaloBundle\Entity\Mensaje',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'mensaje_item',
        );
    }

    public function getName() {
        return 'mensaje';
    }

}
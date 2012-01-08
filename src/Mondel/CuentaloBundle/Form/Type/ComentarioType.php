<?php

namespace Mondel\CuentaloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ComentarioType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('texto', 'text', array('max_length' => 555)
        );
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class'      => 'Mondel\CuentaloBundle\Entity\Comentario',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'comentario_item',
        );
    }

    public function getName() {
        return 'comentario';
    }

}
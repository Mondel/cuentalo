<?php

namespace Mondel\PostBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class CommentType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('text', 'text', array('max_length' => 555));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class'      => 'Mondel\PostBundle\Entity\Comment',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'comment_item',
        );
    }

    public function getName() {
        return 'comment';
    }
}
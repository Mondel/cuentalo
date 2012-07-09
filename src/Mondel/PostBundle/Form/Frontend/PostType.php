<?php

namespace Mondel\PostBundle\Form\Frontend;

use Doctrine\ORM\EntityRepository,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('category', 'entity', array(
            'empty_value'   => 'Categoría',
            'property'      => 'name',
            'class'         => 'MondelPostBundle:Category',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
            },
        ))
        ->add('genre', 'choice', array(
            'choices'     => array('m' => 'Masculino', 'f' => 'Femenino', 'i' => 'Indefinido'),
            'empty_value' => 'Sexo'
        ))
        ->add('text', null, array(
            'max_length'     => 555,
            'required'       => true,
            'error_bubbling' => true
        ))
		->add('video_url', 'hidden');
    }

    public function getDefaultOptions() {
        return array(
            'data_class'      => 'Mondel\PostBundle\Entity\Post',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'post_item',
        );
    }

    public function getName() {
        return 'post';
    }
}
<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Contenido,
    Mondel\CuentaloBundle\Entity\Comentario;
use Mondel\CuentaloBundle\Form\Type\ContenidoType,
    Mondel\CuentaloBundle\Form\Type\ComentarioType;

class DefaultController extends Controller
{
    public function inicioAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();

        $repositorio = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido');

        $contenidos = $repositorio->findBy(
                array('activo' => '1'),
                array('fecha_creacion' => 'DESC')
        );

        // Creo los formularios de comentarios para cada contenido
        $formularios_comentarios = array();
        foreach ($contenidos as $contenido) {
            $comentario = new Comentario();
            $formulario_comentario = $this->createForm(new ComentarioType(), $comentario);
            $formularios_comentarios[$contenido->getId()] = $formulario_comentario->createView();
        }

        // Creo el formulario para pasarle al crear de Contenido
        $contenido = new Contenido();
        $formulario_contenido = $this->createForm(new ContenidoType(), $contenido);

        return $this->render(
                'MondelCuentaloBundle:Default:inicio.html.twig',
                array(
                    'contenidos'    => $contenidos,
                    'form'          => $formulario_contenido->createView(),
                    'formularios_comentarios' => $formularios_comentarios
                )
        );
    }
}

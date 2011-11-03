<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Contenido,
    Mondel\CuentaloBundle\Entity\Comentario,
    Mondel\CuentaloBundle\Entity\Usuario;
use Mondel\CuentaloBundle\Form\Type\ContenidoType,
    Mondel\CuentaloBundle\Form\Type\ComentarioType,
    Mondel\CuentaloBundle\Form\Type\UsuarioType;
use Mondel\CuentaloBundle\Helpers\ObjectHelper;

class DefaultController extends Controller
{
    public function indexAction($tipo='')
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $repository = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido');

        $ultimos_contenidos = $repository
                ->findBy(
                        array(),
                        array(
                            'fecha_creacion' => 'DESC'
                        ),
                        5
                );

        $em = $this->get('doctrine.orm.entity_manager');
        $dql = "SELECT c FROM MondelCuentaloBundle:Contenido c WHERE c.activo = '1' ORDER BY c.fecha_creacion DESC";
        $query = $em->createQuery($dql);

        if (isset($tipo) && !empty($tipo) && $tipo != null) {
            $ultimos_contenidos = $repository->findBy(array('tipo' => $tipo));

            $dql = "SELECT c FROM MondelCuentaloBundle:Contenido c WHERE c.activo = '1' and c.tipo = '" . $tipo . "' ORDER BY c.fecha_creacion DESC";
            $query = $em->createQuery($dql);
        }

        $adapter = $this->get('knp_paginator.adapter');
        $adapter->setQuery($query);
        $adapter->setDistinct(true);

        $paginator = new \Zend\Paginator\Paginator($adapter);
        $paginator->setCurrentPageNumber($this->get('request')->query->get('page', 1));
        $paginator->setItemCountPerPage(5);
        $paginator->setPageRange(5);

        $ultimos_contenidos = $paginator;

        $contenido = new Contenido();
        $form = $this->createForm(new ContenidoType(), $contenido);

        $usuarioR = new Usuario();
        $formR = $this->createForm(new UsuarioType(), $usuarioR);

        $forms_comentario = array();
        $i = 1;
        foreach ($ultimos_contenidos as $ultimo_contenido) {
            $comentario = new Comentario();
            $formC = $this->createForm(new ComentarioType(), $comentario);

            $forms_comentario[$i++] = $formC->createView();
        }

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $contenido->setIp($request->getClientIp());

                $usuario = $this->get('security.context')->getToken()->getUser();
                if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $contenido->setUsuario($usuario);
                    $contenido->setSexo($usuario->getSexo());
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($contenido);
                $em->flush();

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Default:index.html.twig',
                array(
                    'ultimos_contenidos'    => $ultimos_contenidos,
                    'form'                  => $form->createView(),
                    'forms_comentario'      => $forms_comentario,
                    'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
                    'error'                 => $error,
                    'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
                    'formR'                 => $formR->createView(),
                )
        );
    }
}

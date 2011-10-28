<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Contenido;
use Mondel\CuentaloBundle\Form\Type\ContenidoType;
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
                        array('fecha_creacion' => 'DESC'),
                        5
                );

        if (isset($tipo) && !empty($tipo) && $tipo != null) {
            $ultimos_contenidos = $repository->findBy(array('tipo' => $tipo));
        }

        $contenido = new Contenido();
        $form = $this->createForm(new ContenidoType(), $contenido);

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
                    'ultimos_contenidos'  => $ultimos_contenidos,
                    'form'              => $form->createView(),
                    'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
                    'error'             => $error,
                )
        );
    }
}

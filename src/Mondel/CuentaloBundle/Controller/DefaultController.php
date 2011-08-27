<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Contenido;
use Mondel\CuentaloBundle\Form\Type\ContenidoType;
use Mondel\CuentaloBundle\Helpers\ObjectHelper;

class DefaultController extends Controller
{    
    public function indexAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        
        $ultimos_mensajes = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->findBy(
                        array('tipo' => 'm'), 
                        array('fecha_creacion' => 'DESC'), 
                        10
                );
                
        $ultimas_anecdotas = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->findBy(
                        array('tipo' => 'a'),
                        array('fecha_creacion' => 'DESC'), 
                        10
                );
        
        $ultimos_secretos = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->findBy(
                        array('tipo' => 's'),
                        array('fecha_creacion' => 'DESC'), 
                        10
                );
        
        $form = $this->createForm(new ContenidoType(), array());
        
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $contenidoObj = new Contenido();
                $data = $form->getData();
                
                $contenido = ObjectHelper::getObject($contenidoObj, $data);
                $contenido->setIp($request->getClientIp());
                
                $usuario = $this->get('security.context')->getToken()->getUser();
                if ($usuario != 'anon.') {
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
                    'ultimos_mensajes'  => $ultimos_mensajes,                    
                    'ultimos_secretos'  => $ultimos_secretos,
                    'ultimas_anecdotas' => $ultimas_anecdotas,
                    'form'              => $form->createView(),
                    'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
                    'error'             => $error,
                )
        );
    }
}

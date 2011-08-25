<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mondel\CuentaloBundle\Form\Type\ContenidoType;

class DefaultController extends Controller
{    
    public function indexAction()
    {
        $ultimos_mensajes = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->findBy(array('tipo' => 'm'));
        
        $form = $this->createForm(new ContenidoType(), array());      
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {                
                $session = $this->get('request')->getSession();
                $session->setFlash('notice', 'Su mensaje se ha publicado');
                
                $usuario = $this->get('security.context')->getToken()->getUser();
                
                $contenido = $form->getData();                
                $contenido->setUsuario($usuario);
                $contenido->setIp($request->getClientIp());
                
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($contenido);
                $em->flush();
                
                return $this->redirect($this->generateUrl('homepage'));
            }
        }
        
        return $this->render(
                'MondelCuentaloBundle:Default:index.html.twig',
                array(
                    'ultimos_mensajes' => $ultimos_mensajes,
                    'form' => $form->createView(),
                )
        );
    }
}

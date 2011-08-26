<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mondel\CuentaloBundle\Form\Type\ComentarioType;

class ContenidoController extends Controller
{    
    public function mostrarAction($id, $tipo, $titulo)
    {
        $contenido = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->find($id);
        
        $request = $this->getRequest();        
        
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {        
            
            $form = $this->createForm(new ComentarioType(), array());        
            
            if ($request->getMethod() == 'POST') {
                $form->bindRequest($request);
                if ($form->isValid()) {
                    $comentario = $form->getData();
                    $comentario->setIp($request->getClientIp());
                    
                    $usuario = $this->get('security.context')->getToken()->getUser();
                    $comentario->setUsuario($usuario);
                    $comentario->setContenido($contenido);
                    
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($comentario);
                    $em->flush();
                }
            }
            return $this->render(
                'MondelCuentaloBundle:Contenido:mostrar.html.twig',
                array('contenido' => $contenido, 'form' => $form->createView())
            );
            
        }
        
        return $this->render(
                'MondelCuentaloBundle:Contenido:mostrar.html.twig',
                array('contenido' => $contenido)
        );
    }
}

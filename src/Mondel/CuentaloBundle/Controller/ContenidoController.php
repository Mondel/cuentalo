<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Mondel\CuentaloBundle\Entity\Comentario;
use Mondel\CuentaloBundle\Form\Type\ComentarioType;
use Mondel\CuentaloBundle\Helpers\ObjectHelper;

class ContenidoController extends Controller
{    
    public function mostrarAction($id, $tipo, $titulo)
    {
        $contenido = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->find($id);
        
        $request = $this->getRequest();
            
        $form = $this->createForm(new ComentarioType(), array());        

        if ($request->getMethod() == 'POST') {

            if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
                throw new AccessDeniedException();
            }

            $form->bindRequest($request);
            if ($form->isValid()) {                
                /*
                Antes: $comentario = $form->getData();
                */
                
                $data = $form->getData();                
                $comentarioObj = new Comentario();
                $comentario = ObjectHelper::getObject($comentarioObj, $data);
            
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
}

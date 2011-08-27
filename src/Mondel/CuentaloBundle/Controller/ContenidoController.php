<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Mondel\CuentaloBundle\Entity\Comentario,
    Mondel\CuentaloBundle\Entity\Voto;
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
    
    public function votarAction($id)
    {
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        } else {
            
            $contenido = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:Contenido')
                    ->find($id);
            
            if (!$contenido) {
                throw $this->createNotFoundException('No existe ese contenido '.$id);
            }
            
            $voto = new Voto();
            $voto->setContenido($contenido);
            
            $request = $this->getRequest();
            $voto->setIp($request->getClientIp());
            
            $usuario = $this->get('security.context')->getToken()->getUser();
            $voto->setUsuario($usuario);
            
            foreach ($contenido->getVotos() as $_voto) {                
                if ($voto->getUsuario()->getUsername() == $_voto->getUsuario()->getUsername()) {
                    throw $this->createNotFoundException('Error, usted ya ha votado');
                }
            }
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($voto);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl(
                'vista_contenido', 
                array(
                    'id'     => $contenido->getId(), 
                    'tipo'   => $contenido->getTipo(),
                    'titulo' => $contenido->getSlug()
                )
        ));
                
    }
    
}

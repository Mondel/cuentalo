<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mondel\CuentaloBundle\Form\Type\UsuarioType;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        $request = $this->getRequest();
        $form = $this->createForm(new UsuarioType(), array());        
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {                
                $usuario = $form->getData();
                
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($usuario);
                $em->flush();
                
                return $this->redirect($this->generateUrl('_homepage2'));
            }
        }
        
        return $this->render(
                'MondelCuentaloBundle:Default:index.html.twig',
                array('form' => $form->createView())
        );
    }
}

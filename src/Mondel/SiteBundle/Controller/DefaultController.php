<?php

namespace Mondel\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Mondel\CuentaloBundle\Entity\Contenido,
	Mondel\CuentaloBundle\Entity\Comentario,
	Mondel\CuentaloBundle\Form\Type\ContenidoType,
	Mondel\CuentaloBundle\Form\Type\ComentarioType;

class DefaultController extends Controller
{
    public function staticPageAction($page)
    {
    	$translate = array('ayuda' => 'help');

        return $this->render('MondelSiteBundle:Default:' . $translate[$page] . '.html.twig');
    }

    public function homePageAction()
    {
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $em         = $this->getDoctrine()->getEntityManager();
        $posts      = $em->getRepository('MondelCuentaloBundle:Contenido')->findBy(
        	array('activo' => '1'),
        	array('fecha_creacion' => 'DESC'),
        	5
    	);
        
        $commentsForms = array();
        foreach ($posts as $post) {
        	$comment = new Comentario();
            $commentForm = $this->createForm(new ComentarioType(), $comment);
            $commentsForms[$post->getId()] = $commentForm->createView();
        }

        $post     = new Contenido();
        $postForm = $this->createForm(new ContenidoType(), $post);

        return $this->render('MondelSiteBundle:Default:home_page.html.twig', array(
        	'posts'         => $posts,
            'form'          => $postForm->createView(),
            'commentsForms' => $commentsForms,
        	'cid'		    => '0'
    	));
    }
}

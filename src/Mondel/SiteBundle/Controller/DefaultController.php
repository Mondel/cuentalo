<?php

namespace Mondel\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Mondel\PostBundle\Entity\Post,
	Mondel\PostBundle\Entity\Comment,
	Mondel\PostBundle\Form\Frontend\PostType,
	Mondel\PostBundle\Form\Frontend\CommentType;

class DefaultController extends Controller
{
    public function homePageAction()
    {
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $em         = $this->getDoctrine()->getEntityManager();
        $posts      = $em->getRepository('MondelPostBundle:Post')->findBy(
        	array('is_active' => '1'),
        	array('created_at' => 'DESC'),
        	5
    	);
        
        $commentsForms = array();
        foreach ($posts as $post) {
        	$comment = new Comment();
            $commentForm = $this->createForm(new CommentType(), $comment);
            $commentsForms[$post->getId()] = $commentForm->createView();
        }

        $post     = new Post();
        $postForm = $this->createForm(new PostType(), $post);

        return $this->render('MondelSiteBundle:Default:home_page.html.twig', array(
        	'posts'         => $posts,
            'form'          => $postForm->createView(),
            'commentsForms' => $commentsForms,
        	'cid'		    => '0'
    	));
    }

    public function staticPageAction($page)
    {
        $translate = array('ayuda' => 'help');

        return $this->render('MondelSiteBundle:Default:' . $translate[$page] . '.html.twig');
    }
}

<?php

namespace Mondel\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Mondel\PostBundle\Entity\Comment,
    Mondel\PostBundle\Entity\Post,
	Mondel\PostBundle\Form\Frontend\CommentType,    
    Mondel\PostBundle\Form\Frontend\PostType,
    Mondel\UserBundle\Entity\User,
    Mondel\UserBundle\Form\Frontend\UserType;

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

        $user     = new User();
        $userForm = $this->createForm(new UserType(), $user);

        return $this->render('MondelSiteBundle:Default:home.html.twig', array(        	
            'cid'           => '0',
            'commentsForms' => $commentsForms,
            'posts'         => $posts,
            'postForm'      => $postForm->createView(),
            'userForm'      => $userForm->createView()
    	));
    }

    public function staticPageAction($page)
    {
        $translate = array('ayuda' => 'help');

        return $this->render('MondelSiteBundle:Default:' .
            (in_array($page, $translate) ? $translate[$page] : $page) . '.html.twig');
    }
}

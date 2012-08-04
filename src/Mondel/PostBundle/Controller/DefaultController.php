<?php

namespace Mondel\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
	Mondel\PostBundle\Entity\Comment,
    Mondel\PostBundle\Entity\Post,
    Mondel\UserBundle\Entity\Notification,
    Mondel\UserBundle\Entity\UserPostSuscription,
    Mondel\PostBundle\Form\Frontend\CommentType,
	Mondel\PostBundle\Form\Frontend\PostType;

class DefaultController extends Controller
{
    public function createAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        
        $post = new Post();
        
        $form = $this->createForm(new PostType(), $post);
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            $post->setIp($request->getClientIp());

            $em = $this->getDoctrine()->getEntityManager();
            if ($this->get('security.context')->isGranted("ROLE_USER")) {
                $user = $this->get('security.context')->getToken()->getUser();
                $post->setUser($user);

                $suscription = new UsuarioContenidoSuscripcion();
                $suscription->setUser($user);
                $suscription->setPost($post);
                $em->persist($suscription);
            }

            $em->persist($post);            
            $em->flush();
        } else {
            $errors = $form->getErrors();
        }

        if ($request->isXmlHttpRequest()) {
            return new Response($post->getText());
        }

        //TODO: Perdi los errores del formulario
        return $this->redirect($this->generateUrl('home_page'));
    }

    public function showCategoryPostsAction($categoryName)
    {
        $categoryRepository = $this->getDoctrine()->getRepository('MondelPostBundle:Category');
        $category           = $categoryRepository->findOneBy(array(
            'name' => $categoryName
        ));
        
        if (!$category) {
            throw $this->createNotFoundException('La categoría que estas buscando no existe');
        }
        
        $em             = $this->getDoctrine()->getEntityManager();
        $postRepository = $this->getDoctrine()->getRepository('MondelPostBundle:Post');
        $posts          = $postRepository->findBy(
            array('is_active' => '1', 'category' => $category->getId()),
            array('created_at' => 'DESC'),
            5
        );        
        
        $commentsForms = array();
        foreach ($posts as $post) {
            $comment = new Comment();
            $commentForm = $this->createForm(new CommentType(), $comment);
            $commentsForms[$post->getId()] = $commentForm->createView();
        }

        $post = new Post();
        $postForm = $this->createForm(new PostType(), $post);
        
        return $this->render('MondelSiteBundle:Default:home_page.html.twig', array(
            'pagina_titulo' => $category->getName(),
            'posts'         => $posts,
            'form'          => $postForm->createView(),
            'commentsForms' => $commentsForms,
            'cid'           => $category->getId()
        ));
    }

    public function showPostPageAction($postId)
    {
        $postRepository = $this->getDoctrine()->getRepository('MondelPostBundle:Post');
        $post           = $postRepository->find($postId);

        if (!$post || !$post->isActive()) {
            throw $this->createNotFoundException('El post que intentas ver no existe o esta desactivado');
        }

        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->get('security.context')->getToken()->getUser();
            if ($user->isPostSuscribed($postId)) {
                $userRepository = $this->getDoctrine()->getRepository('MondelUserBundle:User');
                $userRepository->readNotifications($user->getId(), $postId);
            }
        }

        return $this->render('MondelPostBundle:Default:show_post_page.html.twig', array(
            'post'  => $post,
            'commentsForms' => array($postId => $form->createView())
        ));
    }
}

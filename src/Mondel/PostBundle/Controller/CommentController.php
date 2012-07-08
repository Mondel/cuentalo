<?php

namespace Mondel\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Mondel\PostBundle\Entity\Comment,
    Mondel\PostBundle\Entity\Post,
    Mondel\UserBundle\Entity\Notification,
    Mondel\UserBundle\Entity\UserPostSuscription,
    Mondel\PostBundle\Form\Frontend\CommentType,
	Mondel\PostBundle\Form\Frontend\PostType;

class CommentController extends Controller
{
	public function createAction($postId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $request = $this->getRequest();
        $session = $request->getSession();
        $em      = $this->getDoctrine()->getEntityManager();
        $post    = $em->getRepository('MondelPostBundle:Post')->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('El post que intentas comentar no existe');
        }

        $comment = new Comment();
        $form    = $this->createForm(new CommentType(), $comment);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $comment->setIp($request->getClientIp());
            $user = $this->get('security.context')->getToken()->getUser();
            $comment->setUser($user);
            $comment->setPost($post);

        	if (!$user->isPostSuscribed($postId)) {
        		$suscription = new UserPostSuscription();
                $suscription->setUser($user);
                $suscription->setPost($post);
                $em->persist($suscription);  
            }

            $suscriptions = $em->getRepository('MondelUserBundle:UserPostSuscription')->findBy(
                array('post' => $postId, 'user' => $user->getId())
            );

            foreach($suscriptions as $suscription) {
                $notification = new Notification();
                $notification->setUserPostSuscription($suscription);                
                $notification->setText($user->getNick() . ' ha comentado la publicación #' . $postId);
                $em->persist($notification);
                $userSuscription = $suscription->getUser();

                if ($userSuscription->getIsNotificationsActive())
                {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Cuentalo: tienes una nueva notificación')
                        ->setFrom(array('notificaciones@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                        ->setTo($userSuscription->getEmail())
                        ->setBody($this->renderView(
                                'MondelUserBundle:Default:notification_email.html.twig',
                                array(
                                    'notification' => $notification,
                                )
                        ), 'text/html')
                    ;
                    $this->get('mailer')->send($message);
                }
            }

            $em->persist($comment);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('post_page', array('postId' => $postId)));
    }    
}


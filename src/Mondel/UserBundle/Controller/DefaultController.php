<?php

namespace Mondel\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Symfony\Component\Security\Core\SecurityContext,
	Mondel\UserBundle\Entity\User,
    Mondel\UserBundle\Entity\UserActivation,
	Mondel\UserBundle\Form\Frontend\UserType;

class DefaultController extends Controller
{	
	public function loginAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();		
		$error   = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR, 
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

		return $this->render('MondelUserBundle:Default:login.html.twig', array(
			'last_username' => $session->get(SecurityContext::LAST_USERNAME),
			'error'         => $error
		));
	}

	public function registerAction()
	{
		$user         = new User();
		$em           = $this->getDoctrine()->getEntityManager();
		$form         = $this->createForm(new UserType(), $user);        
        $request      = $this->getRequest();
        $session      = $request->getSession();
        $errorMessage = '';

        if ($request->getMethod() == 'POST') {        	
            $form->bindRequest($request);

            if ($form->isValid()) {            	
                $userRepository = $em->getRepository('MondelUserBundle:User');
                $userEmailExist = $userRepository->findOneBy(array(
                	'email' => $user->getEmail(), 
                	'is_active' => true
            	));
            	$userNickExist = $userRepository->findOneBy(array(
            		'nick' => $user->getNick(), 
            		'is_active' => true
        		));

                if($userEmailExist) {
                    $errorMessage = 'El email indicado ya fue registrado con anterioridad';
                } else if ($userNickExist) {
                    $errorMessage = 'El nick indicado ya está siendo utilizado por otro usuario, por favor ingrese otro';
                }

                if (!empty($errorMessage)) {
                	$session->getFlashBag()->add('error', $errorMessage);
                } else {
                    $user->setSalt(md5(time()));
                    $encoder  = $this->get('security.encoder_factory')->getEncoder($user);
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    
                    $user->setPassword($password);
                    $em->persist($user);

                    $token = uniqid();
                    $userActivationRepository = 
                        $em->getRepository('MondelUserBundle:UserActivation');

                    $userActivation = $userActivationRepository->findOneBy(array(
                        'email' => $user->getEmail()
                    ));

                    if ($userActivation) {
                        $token = $userActivation->getToken();
                    } else {
                        $userActivation = new UserActivation();
                        $userActivation->setEmail($user->getEmail());
                        $userActivation->setToken($token);
                        $em->persist($userActivation);
                    }

                    $em->flush();
                    
                    $userName = $user->getName() != '' ? $user->getName() : $user->getNick();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Cuentalo: email de activación')
                        ->setFrom(array('registros@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                        ->setTo($user->getEmail())
                        ->setBody($this->renderView(
                                'MondelUserBundle:Default:register_email.html.twig',
                                array(
                                    'token' => $token,
                                    'name' => $userName,
                                )
                        ), 'text/html');

                    $this->get('mailer')->send($message);

                    $session->getFlashBag()->add(
                        'notice', 
                        'Se ha enviado un email a tu casilla de correo (' . $user->getEmail() . ')');

                    //return $this->redirect($this->generateUrl('user_login'));
                }
            }
        }

        return $this->render('MondelUserBundle:Default:register.html.twig', array(
			'form' => $form->createView()
		));
	}

    public function activateUserAction()
    {
        $userActivationRepository = $this->getDoctrine()->getRepository(
            'MondelUserBundle:UserActivation'
        );

        $userActivation = $userActivationRepository->findOneBy(array(
            'token' => $token,
        ));

        if ($userActivation) {
            $email = $userActivation->getEmail();

            $userRepository = $this->getDoctrine()->getRepository('MondelUserBundle:User');

            $user = $userRepository->findOneBy(array(
                'email' => $email
            ));

            $user->setIsActive(true);
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($userActivation);
            $em->flush();

            // logging the user
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.context')->setToken($token);
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add(
                'error', 
                'Hubo un error al activar este usuario. El link que has seguido es incorrecto o ya fue utilizado.'
            );
        }

        return $this->redirect($this->generateUrl('user_register'));
    }

    public function recoverPasswordAction()
    {    
        $request = $this->getRequest();
        $session = $request->getSession();

        $validations = new Collection(array(
            'email' => new Email(array('message' => 'Ingresa una dirección de correo electrónico')),
        ));

        $form = $this->createFormBuilder(null, array(
            'validation_constraint' => $validations
        ))->add('email', 'email')->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            
            $email = $form->getData();
            $email = $email['email'];

            $userRepository = $this->getDoctrine()->getRepository('MondelUserBundle:User');
            $user = $userRepository->findOneBy(array('email' => $email));

            if ($user) {
                $newPassword = base_convert(mt_rand(0x19A100, 0x39AA3FF), 10, 36);

                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $password = $encoder->encodePassword(
                        $newPassword,
                        $user->getSalt()
                );
                $user->setContrasenia($password);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                $userName = $user->getName() != '' ? $user->getName() : $user->getNick();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Cuentalo: recuperando la contraseña')
                    ->setFrom(array('registros@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView(
                        'MondelUserBundle:Default:recover_password_email.html.twig',
                        array(
                            'password' => $newPassword,
                            'name' => $userName,
                        )
                    ), 'text/html');

                $this->get('mailer')->send($message);
                $session->getFlashBag()->add(
                    'notice', 
                    'Se ha enviado un email a tu casilla de correo (' . $user->getEmail() . ')');
            } else {
                $session->getFlashBag()->add(
                    'error', 
                    'El correo que esta utilizando no esta registrado'
                );
            }
        }

        return $this->render(
            'MondelUserBundle:User:recover_password.html.twig',
            array('form' => $form->createView())
        );
    }

    public function changePasswordAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $session->getFlashBag()->remove('notice');
        $session->getFlashBag()->remove('error');
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $form = $this->createFormBuilder()
            ->add('currentPassword', 'password')
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'Las contraseñas no coinciden.',
                'first_name'      => 'password',
                'second_name'     => 'repeatedPassword',
                'required'        => true,
            ))
            ->getForm();
    
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            
            $data = $request->get('form');
            $currentPassword = $data['currentPassword'];           
            $newPassword = $data['password']['password'];
            $repeatedNewPassword = $data['password']['repeatedPassword'];
            
            if ($newPassword == $repeatedNewPassword) {
                $user = $this->get('security.context')->getToken()->getUser();
                
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $isPasswordValid = $encoder->isPasswordValid($user->getPassword(), $currentPassword, $user->getSalt());
                if ($isPasswordValid)
                {
                    $newPassword = $encoder->encodePassword(
                        $newPassword,
                        $user->getSalt()
                    );
                    $user->setPassword($newPassword);
                
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($user);
                    $em->flush();
                
                    $session->getFlashBag->add(
                        'notice', 
                        'Se ha cambiado la contraseña correctamente'
                    );
                } else {
                    $session->getFlashBag->add('error', 'La contraseña actual es incorrecta');
                }
            } else {
                $session->getFlashBag->add(
                    'error', 
                    'La contraseña y su repetida deben ser iguales'
                );
            }
        }
    
        return $this->render(
            'MondelUserBundle:User:change_password.html.twig',
            array('form' => $form->createView())
        );
    }
    
    public function usuarioEliminarAction()
    {
        $this->get('session')->removeFlash("notice");
        $this->get('session')->removeFlash("error");
    
        if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
    
        $peticion = $this->getRequest();
    
        $formulario = $this->createFormBuilder()
            ->add('contraseniaActual', 'password')          
            ->getForm();
    
        if ($peticion->getMethod() == 'POST') {
            $formulario->bindRequest($peticion);
    
            $data = $peticion->get('form');
            $contrasenia_actual = $data['contraseniaActual'];
    
            $usuario = $this->get('security.context')->getToken()->getUser();
    
            $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
            if ($encoder->isPasswordValid($usuario->getPassword(), $contrasenia_actual, $usuario->getSalt()))
            {
                $em = $this->getDoctrine()->getEntityManager();
                foreach ($usuario->getComentarios() as $comentario) {
                    $em->remove($comentario);
                }
                foreach ($usuario->getContenidos() as $contenido) {
                    $contenido->setUsuario(null);
                }
                $em->remove($usuario);
                $em->flush();
    
                //Desloguear al usuario dado de baja
                $this->get("request")->getSession()->invalidate();
                $this->get('security.context')->setToken(null);
                $this->get('session')->setFlash('notice', "Se ha dado de baja su cuenta. Por cualquier información utilice la página de contacto");
                return $this->redirect($this->generateUrl('_inicio'));
            } else {
                $this->get('session')->setFlash('error', 'La contraseña actual es incorrecta');
            }           
        }
    
        return $this->render(
                'MondelCuentaloBundle:Usuario:usuarioEliminar.html.twig',
                array('form' => $formulario->createView())
        );
    }
    
    public function usuarioActivacionAction($token)
    {
        $repositorio = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:UsuarioActivacion');

        $activacion = $repositorio->findOneBy(array(
            'token' => $token,
        ));

        if ($activacion != null) {
            $email = $activacion->getEmail();

            $repositorio = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:Usuario');

            $usuario = $repositorio->findOneBy(array(
                'email' => $email,
            ));

            $usuario->setActivo(true);
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($activacion);
            $em->flush();

            // Logueamos al usuario
            $token = new UsernamePasswordToken($usuario, null, 'main', $usuario->getRoles());
            $this->get('security.context')->setToken($token);
        } else {
            $this->get('session')->setFlash('error', 'Hubo un error al activar este usuario. El link que has seguido es incorrecto o ya fue utilizado.');
        }

        return $this->redirect($this->generateUrl('_inicio'));
    }

    public function notificacionesListarAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        $usuario = $this->get('security.context')->getToken()->getUser();        
        $notificaciones = $usuario->obtenerNotificaciones();

        $formulario = $this->createFormBuilder(array('recibirNotificacionEmail' => $usuario->getRecibeNotificaciones()))
                ->add('recibirNotificacionEmail', 'checkbox', array(
                    'label'     => 'Recibir notificaciones por email',
                    'required'  => false,
                ))
                ->getForm();
    
        $peticion = $this->getRequest();
        if ($peticion->getMethod() == 'POST') {
            $formulario->bindRequest($peticion);
            
            $data = $peticion->get('form');
            $em = $this->getDoctrine()->getEntityManager(); 
            $usuario = $em->getRepository('MondelCuentaloBundle:Usuario')
                ->find($usuario->getId());
            $usuario->setRecibeNotificaciones(isset($data['recibirNotificacionEmail']));
            $em->flush();
            $this->get('session')->setFlash('notice', 'Tus cambios se han guardado correctamente!');
        } else {
            $this->get('session')->removeFlash('notice');
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:notificacionesListar.html.twig',
                array(
                    'notificaciones' => $notificaciones,
                    'form'           => $formulario->createView(),
                )

        );        
    }

    public function mensajesListarAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        $usuario = $this->get('security.context')->getToken()->getUser();        
        $mensajes_recibidos = $usuario->getMensajesRecibidos();
        $mensajes_enviados = $usuario->getMensajesEnviados();

        return $this->render(
                'MondelCuentaloBundle:Usuario:mensajes.html.twig',
                array(
                    'mensajes_recibidos' => $mensajes_recibidos,
                    'mensajes_enviados' => $mensajes_enviados,
                )

        ); 
    }

    public function mensajesCrearAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        $manager = $this->getDoctrine()->getEntityManager();
        $peticion = $this->getRequest();

        $mensaje = new Mensaje();
        $formulario = $this->createForm(new MensajeType(), $mensaje);

        $usuario = $this->get('security.context')->getToken()->getUser();
        $mensaje->setUsuarioRemitente($usuario);

        if ($peticion->getMethod() == 'POST') {
            
            $formulario->bindRequest($peticion);

            if ($formulario->isValid()) {
                
                //print_r($formulario->getData());

                
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:mensajesCrear.html.twig',
                array('form' => $formulario->createView())
        );
    }
}
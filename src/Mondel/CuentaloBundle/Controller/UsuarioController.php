<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Usuario,
    Mondel\CuentaloBundle\Entity\Mensaje,
    Mondel\CuentaloBundle\Entity\UsuarioActivacion,
    Mondel\CuentaloBundle\Entity\UsuarioRegistro;
use Mondel\CuentaloBundle\Form\Type\MensajeType;
use Symfony\Component\Validator\Constraints\Email,
    Symfony\Component\Validator\Constraints\MinLength,
    Symfony\Component\Validator\Constraints\Collection;

class UsuarioController extends Controller
{
    public function ingresoAction()
    {
        if (true === $this->get('security.context')
                ->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('_inicio'));
        }

        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();

        if ($peticion->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $peticion->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $sesion->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        if ($sesion->get(SecurityContext::LAST_USERNAME) != '') {
	        $usuarioRegistro = new UsuarioRegistro();        
	        $usuarioRegistro->setEmail($sesion->get(SecurityContext::LAST_USERNAME));
	        $usuarioRegistro->setIp($peticion->getClientIp());
	        $usuarioRegistro->setFecha(new \DateTime());
	        
	        $manager = $this->getDoctrine()->getEntityManager();
	        $manager->persist($usuarioRegistro);
	        $manager->flush();
        }

        return $this->render('MondelCuentaloBundle:Usuario:ingreso.html.twig', 
        array(            
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    public function registroAction()
    {
        if (true === $this->get('security.context')
                ->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('_inicio'));
        }

        $manager = $this->getDoctrine()->getEntityManager();
        $peticion = $this->getRequest();

        $usuario = new Usuario();
        $formulario = $this->createForm(new UsuarioType(), $usuario);

        if ($peticion->getMethod() == 'POST') {
        	
            $formulario->bindRequest($peticion);

            if ($formulario->isValid()) {
            	
                $repositorio = $manager
                    ->getRepository('MondelCuentaloBundle:Usuario');

                if($repositorio->findOneBy(array('email' => $usuario->getEmail(), 'activo' => true))) {
                    $error = 'El email indicado ya fue registrado con anterioridad';
                } else if ($repositorio->findOneBy(array('nick' => $usuario->getNick(), 'activo' => true))) {
                    $error = 'El nick indicado ya está siendo utilizado por otro usuario, por favor ingrese otro';
                }

                if (isset ($error) && !empty ($error)) {
                    $this->get('session')->setFlash('error', $error);
                } else {
                    $this->get('session')->removeFlash('error');

                    $usuario->setSalt(md5(time()));

                    $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                    $contrasenia = $encoder->encodePassword(
                            $usuario->getContrasenia(),
                            $usuario->getSalt()
                    );
                    $usuario->setContrasenia($contrasenia);


                    $manager->persist($usuario);

                    $token = uniqid();

                    $activaciones = $manager
                            ->getRepository('MondelCuentaloBundle:UsuarioActivacion');

                    $activacion = $activaciones->findOneBy(array(
                        'email' => $usuario->getEmail()
                    ));

                    if ($activacion != null) {
                        $token = $activacion->getToken();
                    } else {
                        $usuario_activacion = new UsuarioActivacion();
                        $usuario_activacion->setEmail($usuario->getEmail());
                        $usuario_activacion->setToken($token);
                        $manager->persist($usuario_activacion);
                    }
                    $manager->flush();

                    $usuario_nombre = $usuario->getNombre() != '' ? $usuario->getNombre() : $usuario->getNick();

                    $mensaje = \Swift_Message::newInstance()
                        ->setSubject('Cuentalo: email de activación')
                        ->setFrom(array('registros@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                        ->setTo($usuario->getUsername())
                        ->setBody($this->renderView(
                                'MondelCuentaloBundle:Usuario:emailRegistro.html.twig',
                                array(
                                    'token' => $token,
                                    'nombre' => $usuario_nombre,
                                )
                        ), 'text/html')
                    ;
                    $this->get('mailer')->send($mensaje);

                    //$this->get('session')->setFlash('notice', "Se ha enviado un email a tu casilla de correo (".$usuario->getUsername().")");

                    // Logueamos al usuario
                    // $token = new UsernamePasswordToken($usuario, null, 'main', $usuario->getRoles());
                    // $this->get('security.context')->setToken($token);

                    return $this->redirect($this->generateUrl(
                        '_pagina', 
                        array('pagina'=> 'registro_correcto')
                    ));
                }
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:registro.html.twig',
                array('form' => $formulario->createView())
        );
    }

    public function contraseniaRecuperarAction()
    {
    	if (true === $this->get('security.context')->isGranted('ROLE_USER'))
    		return $this->redirect($this->generateUrl('_inicio'));
    		
        $peticion = $this->getRequest();

        $validaciones = new Collection(array(
            'email' => new Email(array('message' => 'Ingresa una dirección de correo electrónico')),
        ));

        $formulario = $this->createFormBuilder(null, array(
            'validation_constraint' => $validaciones))
                ->add('email', 'email')
                ->getForm();

        if ($peticion->getMethod() == 'POST') {
            $formulario->bindRequest($peticion);
            $email = $formulario->getData();
            $email = $email['email'];

            $repositorio = $this->getDoctrine()
                        ->getRepository('MondelCuentaloBundle:Usuario');

            $usuario = $repositorio->findOneBy(array('email' => $email));

            if ($usuario != null) {
                $nueva_contrasenia = base_convert(mt_rand(0x19A100, 0x39AA3FF), 10, 36);

                $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                $contrasenia = $encoder->encodePassword(
                        $nueva_contrasenia,
                        $usuario->getSalt()
                );
                $usuario->setContrasenia($contrasenia);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($usuario);
                $em->flush();

                $usuario_nombre = $usuario->getNombre() != '' ? $usuario->getNombre() : $usuario->getNick();

                $mensaje = \Swift_Message::newInstance()
                    ->setSubject('Cuentalo: recuperación de contraseña')
                    ->setFrom(array('registros@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                    ->setTo($usuario->getUsername())
                    ->setBody($this->renderView(
                            'MondelCuentaloBundle:Usuario:emailRecuperacion.html.twig',
                            array(
                                'contrasenia' => $nueva_contrasenia,
                                'nombre' => $usuario_nombre,
                            )
                    ), 'text/html')
                ;
                $this->get('mailer')->send($mensaje);

                $this->get('session')->setFlash('notice', "Se ha enviado un email a tu casilla de correo (".$usuario->getUsername().")");
            } else {
                $this->get('session')->setFlash('error', 'El correo que esta utilizando no esta registrado');
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:contraseniaRecuperar.html.twig',
                array('form' => $formulario->createView())
        );
    }

    public function contraseniaCambiarAction()
    {
    	$this->get('session')->removeFlash("notice");
    	$this->get('session')->removeFlash("error");
    	
    	if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
    	
    	$peticion = $this->getRequest();
    	
    	$formulario = $this->createFormBuilder()
    			->add('contraseniaActual', 'password')
    			->add('contrasenia', 'repeated', array(
                    'type'            => 'password',
                    'invalid_message' => 'Las contraseñas no coinciden.',
                    'first_name'      => 'contrasenia',
                    'second_name'     => 'repetirContrasenia',
                    'required'        => true,
                ))
    			->getForm();
    
    	if ($peticion->getMethod() == 'POST') {
    		$formulario->bindRequest($peticion);
    		
    		$data = $peticion->get('form');
    		$contrasenia_actual = $data['contraseniaActual'];    		
    		$contrasenia_nueva = $data['contrasenia']['contrasenia'];
    		$contrasenia_nueva_repetida = $data['contrasenia']['repetirContrasenia'];
    		
    		if ($contrasenia_nueva == $contrasenia_nueva_repetida) {
    			$usuario = $this->get('security.context')->getToken()->getUser();
    			
    			$encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
    			if ($encoder->isPasswordValid($usuario->getPassword(), $contrasenia_actual, $usuario->getSalt()))
    			{
    				$nueva_contrasenia = $encoder->encodePassword(
    						$contrasenia_nueva,
    						$usuario->getSalt()
    				);
    				$usuario->setContrasenia($nueva_contrasenia);
    			
    				$em = $this->getDoctrine()->getEntityManager();
    				$em->persist($usuario);
    				$em->flush();
    			
    				$this->get('session')->setFlash('notice', "Se ha cambiado la contraseña correctamente");
    			} else {
    				$this->get('session')->setFlash('error', 'La contraseña actual es incorrecta');
    			}
    		} else {
    			$this->get('session')->setFlash('error', 'La contraseña y su repetida deben ser iguales');
    		}
    	}
    
    	return $this->render(
    			'MondelCuentaloBundle:Usuario:contraseniaCambiar.html.twig',
    			array('form' => $formulario->createView())
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

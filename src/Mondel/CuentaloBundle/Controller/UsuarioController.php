<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Usuario,
    Mondel\CuentaloBundle\Entity\UsuarioActivacion;
use Mondel\CuentaloBundle\Form\Type\UsuarioType;
use Mondel\CuentaloBundle\Helpers\ObjectHelper;

class UsuarioController extends Controller
{
    public function loginAction()
    {
        if (true === $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('inicio'));
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('MondelCuentaloBundle:Usuario:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    public function registroAction()
    {
        if (true === $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $request = $this->getRequest();

        $usuario = new Usuario();
        $form = $this->createForm(new UsuarioType(), $usuario);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $repository = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:Usuario');

                $userExist = $repository->findOneBy(array(
                    'email' => $usuario->getEmail(),
                    'activo' => true
                ));

                if ($userExist != null) {
                    $this->get('session')->setFlash('error', 'El email que intenta registrar ya fue registrado con anterioridad.');
                } else {
                    $usuario->setSalt(md5(time()));

                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($usuario);
                    $password = $encoder->encodePassword(
                            $usuario->getContrasenia(),
                            $usuario->getSalt()
                    );
                    $usuario->setContrasenia($password);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($usuario);

                    $token = uniqid();

                    $activaciones = $this->getDoctrine()
                            ->getRepository('MondelCuentaloBundle:UsuarioActivacion');

                    $activacion = $activaciones->findOneBy(array(
                        'email' => $usuario->getEmail()
                    ));

                    if ($activacion != null) {
                        $token = $activacion->getToken();
                    } else {
                        $usuarioActivacion = new UsuarioActivacion();
                        $usuarioActivacion->setEmail($usuario->getEmail());
                        $usuarioActivacion->setToken($token);
                        $em->persist($usuarioActivacion);
                    }
                    $em->flush();

                    // enviamos el email de confirmacion
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Cuentalo: email de activación')
                        ->setFrom('registros@cuentalo.com.uy')
                        ->setTo($usuario->getUsername())
                        ->setBody($this->renderView('MondelCuentaloBundle:Usuario:emailRegistro.html.twig',
                                array('token' => $token)), 'text/html'
                        )
                    ;
                    $this->get('mailer')->send($message);

                    $this->get('session')->setFlash('noticia', "Se ha enviado un email a tu casilla de correo (".$usuario->getUsername().")");

                    // Logueamos al usuario
                    // $token = new UsernamePasswordToken($usuario, null, 'main', $usuario->getRoles());
                    // $this->get('security.context')->setToken($token);

                    return $this->redirect($this->generateUrl('inicio'));
                }
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:registro.html.twig',
                array('form' => $form->createView())
        );
    }

    public function recuperarContraseniaAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $email = $request->get("_email");

            $repository = $this->getDoctrine()
                        ->getRepository('MondelCuentaloBundle:Usuario');

            $userExist = $repository->findOneBy(array('email' => $email));

            if ($userExist != null) {


                $newPassword = base_convert(mt_rand(0x19A100, 0x39AA3FF), 10, 36);

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($userExist);
                $password = $encoder->encodePassword(
                        $newPassword,
                        $userExist->getSalt()
                );
                $userExist->setContrasenia($password);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($userExist);
                $em->flush();

                // enviamos el email de confirmacion
                $message = \Swift_Message::newInstance()
                    ->setSubject('Cuentalo: recuperación de contraseña')
                    ->setFrom('registros@cuentalo.com.uy')
                    ->setTo($userExist->getUsername())
                    ->setBody($this->renderView(
                            'MondelCuentaloBundle:Usuario:emailRecuperacion.html.twig',
                            array('contrasenia' => $newPassword)
                    ))
                ;
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('noticia', "Se ha enviado un email a tu casilla de correo (".$userExist->getUsername().")");
            } else {
                $this->get('session')->setFlash('noticia', 'El correo que esta utilizando no esta registrado');
            }
        } else {
            return $this->render('MondelCuentaloBundle:Usuario:recuperarPrevioContrasenia.html.twig');
        }

        return $this->redirect($this->generateUrl('inicio'));
    }



    public function registroActivarAction($token)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:UsuarioActivacion');

        $activacion = $repository->findOneBy(array(
            'token' => $token,
        ));

        if ($activacion != null) {
            $email = $activacion->getEmail();

            $repository = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:Usuario');

            $usuario = $repository->findOneBy(array(
                'email' => $email,
            ));

            $usuario->setActivo(true);

            $em->remove($activacion);
        } else {
            $this->get('session')->setFlash('error', 'Hubo un error al activar este usuario. El link que has seguido es incorrecto o ya fue utilizado.');
        }

        $em->flush();

        return $this->redirect($this->generateUrl('inicio'));
    }

}

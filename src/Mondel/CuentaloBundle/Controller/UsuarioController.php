<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Usuario;
use Mondel\CuentaloBundle\Form\Type\UsuarioType;
use Mondel\CuentaloBundle\Helpers\ObjectHelper;

class UsuarioController extends Controller
{
    public function loginAction()
    {
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

                $this->get('session')->setFlash('notice', "Se ha enviado un email a tu casilla de correo (".$userExist->getUsername().")");
            } else {
                $this->get('session')->setFlash('notice', 'El correo que esta utilizando no esta registrado');
            }
        } else {
            return $this->render('MondelCuentaloBundle:Usuario:recuperarPrevioContrasenia.html.twig');
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    public function registroAction()
    {
        $request = $this->getRequest();

        $usuario = new Usuario();
        $form = $this->createForm(new UsuarioType(), $usuario);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $repository = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:Usuario');

                $userExist = $repository->findOneBy(array('email' => $usuario->getEmail()));

                if ($userExist != null)
                    return $this->forward('MondelCuentaloBundle:Default:index', array(
                        'customError'  => 'El email que intenta registrar ya existe'
                    ));

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
                $em->flush();

                // enviamos el email de confirmacion
                $message = \Swift_Message::newInstance()
                    ->setSubject('Cuentalo: email de activación')
                    ->setFrom('registros@cuentalo.com.uy')
                    ->setTo($usuario->getUsername())
                    ->setBody($this->renderView('MondelCuentaloBundle:Usuario:emailRegistro.html.twig'))
                ;
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('notice', "Se ha enviado un email a tu casilla de correo (".$usuario->getUsername().")");

                // Logueamos al usuario
                // $token = new UsernamePasswordToken($usuario, null, 'main', $usuario->getRoles());
                // $this->get('security.context')->setToken($token);

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:registro.html.twig',
                array('form' => $form->createView())
        );
    }
}

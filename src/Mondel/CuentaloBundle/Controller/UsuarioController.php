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

                // Logueamos al usuario
                $token = new UsernamePasswordToken($usuario, null, 'main', $usuario->getRoles());
                $this->get('security.context')->setToken($token);

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render(
                'MondelCuentaloBundle:Usuario:registro.html.twig',
                array('form' => $form->createView())
        );
    }
}

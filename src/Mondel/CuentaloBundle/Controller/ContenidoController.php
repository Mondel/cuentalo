<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Mondel\CuentaloBundle\Entity\Contenido,
    Mondel\CuentaloBundle\Entity\Comentario,
    Mondel\CuentaloBundle\Entity\Voto;
use Mondel\CuentaloBundle\Form\Type\ContenidoType,
    Mondel\CuentaloBundle\Form\Type\ComentarioType;

class ContenidoController extends Controller
{

    public function crearAction()
    {
        $peticion = $this->getRequest();

        $contenido = new Contenido();
        $formulario = $this->createForm(new ContenidoType(), $contenido);

        $formulario->bindRequest($peticion);

        if ($formulario->isValid()) {

            $contenido->setIp($peticion->getClientIp());            

            if ($this->get('security.context')->isGranted("ROLE_USER")) {
            	$usuario = $this->get('security.context')->getToken()->getUser();
            	$contenido->setUsuario($usuario);
            }
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($contenido);
            $em->flush();
        }

        //TODO: Perdi los errores del formulario
        return $this->redirect($this->generateUrl('_inicio'));
    }

    public function comentarAction($id)
    {
        $peticion = $this->getRequest();
        $manager = $this->getDoctrine()->getEntityManager();

        $contenido = $manager->getRepository('MondelCuentaloBundle:Contenido')->find($id);

        if (!$contenido)
            throw $this->createNotFoundException('El post que intentas comentar no existe');

        $comentario = new Comentario();
        $formulario = $this->createForm(new ComentarioType(), $comentario);

        $formulario->bindRequest($peticion);

        if ($formulario->isValid()) {

            $comentario->setIp($peticion->getClientIp());
            $usuario = $this->get('security.context')->getToken()->getUser();
            $comentario->setUsuario($usuario);
            $comentario->setContenido($contenido);

            $manager->persist($comentario);
            $manager->flush();
        }
        return $this->redirect($this->generateUrl(
                '_contenido_mostrar',
                array('id' => $id)
        ));
    }

    public function mostrarAction($id)
    {
        $contenido = $this->getDoctrine()->getRepository('MondelCuentaloBundle:Contenido')->find($id);

        if (!$contenido)
            throw $this->createNotFoundException('El post que intentas ver no existe');

        $comentario = new Comentario();
        $formulario = $this->createForm(new ComentarioType(), $comentario);

        return $this->render(
            'MondelCuentaloBundle:Contenido:mostrar.html.twig',
            array(
                'contenido'  => $contenido,
                'formulario' => $formulario->createView()
            )
        );
    }

    public function mostrarMensajesAction()
    {
        $contenido = $this->getDoctrine()
                ->getRepository('MondelCuentaloBundle:Contenido')
                ->findBy(array('tipo' => 'm'));

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
                throw new AccessDeniedException();
            }

            $form->bindRequest($request);
            if ($form->isValid()) {
                $comentario->setIp($request->getClientIp());

                $usuario = $this->get('security.context')->getToken()->getUser();
                $comentario->setUsuario($usuario);
                $comentario->setContenido($contenido);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($comentario);
                $em->flush();
            }
        }

        return $this->render(
            'MondelCuentaloBundle:Contenido:mostrar.html.twig',
            array('contenido' => $contenido, 'form' => $form->createView())
        );

    }

    public function votarAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        } else {

            $contenido = $this->getDoctrine()
                    ->getRepository('MondelCuentaloBundle:Contenido')
                    ->find($id);

            if (!$contenido) {
                throw $this->createNotFoundException('No existe ese contenido '.$id);
            }

            $voto = new Voto();
            $voto->setContenido($contenido);

            $request = $this->getRequest();
            $voto->setIp($request->getClientIp());

            $usuario = $this->get('security.context')->getToken()->getUser();
            $voto->setUsuario($usuario);

            foreach ($contenido->getVotos() as $_voto) {
                if ($voto->getUsuario()->getUsername() == $_voto->getUsuario()->getUsername()) {
                    throw $this->createNotFoundException('Error, usted ya ha votado');
                }
            }

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($voto);
            $em->flush();
        }

        return $this->redirect($this->generateUrl(
                'vista_contenido',
                array(
                    'id'     => $contenido->getId(),
                    'tipo'   => $contenido->getTipo()
                )
        ));

    }

}

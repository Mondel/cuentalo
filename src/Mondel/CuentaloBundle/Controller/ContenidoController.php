<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

use Mondel\CuentaloBundle\Entity\Contenido,
    Mondel\CuentaloBundle\Entity\Comentario,
    Mondel\CuentaloBundle\Entity\Notificacion,
    Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion,
    Mondel\CuentaloBundle\Entity\Voto;
use Mondel\CuentaloBundle\Form\Type\ContenidoType,
    Mondel\CuentaloBundle\Form\Type\ComentarioType;
use Mondel\CuentaloBundle\Helpers\StringHelper;

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
            $contenido->setTexto(
                StringHelper::limpiar_malas_palabras(
                    $contenido->getTexto()
                )
            );

            $em = $this->getDoctrine()->getEntityManager();
            if ($this->get('security.context')->isGranted("ROLE_USER")) {
                $usuario = $this->get('security.context')->getToken()->getUser();
                $contenido->setUsuario($usuario);

                $suscripcion = new UsuarioContenidoSuscripcion();
                $suscripcion->setUsuario($usuario);
                $suscripcion->setContenido($contenido);
                $em->persist($suscripcion);
            }

            $em->persist($contenido);            
            $em->flush();
        }

        //TODO: Perdi los errores del formulario
        return $this->redirect($this->generateUrl('home_page'));
    }


    public function comentarAction($id)
    {
        $peticion = $this->getRequest();
        $manager = $this->getDoctrine()->getEntityManager();

       	if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
        
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

            $comentario->setTexto(
                StringHelper::limpiar_malas_palabras(
                    $comentario->getTexto()
                )
            );

            $query_builder = $manager->createQueryBuilder();
            $query_builder->add('select', 's')
                ->add('from', 'Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion s')
                ->add('where', 's.usuario = ?1 and s.contenido = ?2')
                ->setParameters(array(1 => $usuario, 2 => $contenido));
            
            if (count($query_builder->getQuery()->getArrayResult()) == 0) {
                $suscripcion = new UsuarioContenidoSuscripcion();
                $suscripcion->setUsuario($usuario);
                $suscripcion->setContenido($contenido);
                $manager->persist($suscripcion);                
            }

            $query_builder = $manager->createQueryBuilder();
            $query_builder->add('select', 's')
                ->add('from', 'Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion s')
                ->add('where', 's.contenido = ?1 and s.usuario != ?2')
                ->setParameters(array(1 => $contenido, 2 => $usuario));

            foreach($query_builder->getQuery()->getResult() as $suscripcion) {
                $notificacion = new Notificacion();
                $notificacion->setUsuarioContenidoSuscripcion($suscripcion);                
                $notificacion->setTexto($usuario->getNick() . ' ha comentado la publicación #' . $contenido->getId());
                $notificacion->setLeida(false);
                $manager->persist($notificacion);

                if ($suscripcion->getUsuario()->getRecibeNotificaciones())
                {
                    $mensaje = \Swift_Message::newInstance()
                        ->setSubject('Cuentalo: tienes una nueva notificación')
                        ->setFrom(array('notificaciones@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                        ->setTo($suscripcion->getUsuario()->getEmail())
                        ->setBody($this->renderView(
                                'MondelCuentaloBundle:Usuario:emailNotificacion.html.twig',
                                array(
                                    'notificacion' => $notificacion,
                                )
                        ), 'text/html')
                    ;
                    $this->get('mailer')->send($mensaje);
                }
            }

            $manager->persist($comentario);
            $manager->flush();
        }
        return $this->redirect($this->generateUrl(
                '_contenido_pagina_mostrar',
                array('id' => $id)
        ));
    }

    public function comentarAjaxAction($id, $offset)
    {
        $peticion = $this->getRequest();
        $manager = $this->getDoctrine()->getEntityManager();

        if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
        
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

            $comentario->setTexto(
                StringHelper::limpiar_malas_palabras(
                    $comentario->getTexto()
                )
            );

            $query_builder = $manager->createQueryBuilder();
            $query_builder->add('select', 's')
                ->add('from', 'Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion s')
                ->add('where', 's.usuario = ?1 and s.contenido = ?2')
                ->setParameters(array(1 => $usuario, 2 => $contenido));
            
            if (count($query_builder->getQuery()->getArrayResult()) == 0) {
                $suscripcion = new UsuarioContenidoSuscripcion();
                $suscripcion->setUsuario($usuario);
                $suscripcion->setContenido($contenido);
                $manager->persist($suscripcion);                
            }

            $query_builder = $manager->createQueryBuilder();
            $query_builder->add('select', 's')
                ->add('from', 'Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion s')
                ->add('where', 's.contenido = ?1 and s.usuario != ?2')
                ->setParameters(array(1 => $contenido, 2 => $usuario));

            foreach($query_builder->getQuery()->getResult() as $suscripcion) {
                $notificacion = new Notificacion();
                $notificacion->setUsuarioContenidoSuscripcion($suscripcion);                
                $notificacion->setTexto($usuario->getNick() . ' ha comentado la publicación #' . $contenido->getId());
                $notificacion->setLeida(false);
                $manager->persist($notificacion);

                if ($suscripcion->getUsuario()->getRecibeNotificaciones())
                {
                    $mensaje = \Swift_Message::newInstance()
                        ->setSubject('Cuentalo: tienes una nueva notificación')
                        ->setFrom(array('notificaciones@cuentalo.com.uy' => 'www.cuentalo.com.uy'))
                        ->setTo($suscripcion->getUsuario()->getEmail())
                        ->setBody($this->renderView(
                                'MondelCuentaloBundle:Usuario:emailNotificacion.html.twig',
                                array(
                                    'notificacion' => $notificacion,
                                )
                        ), 'text/html')
                    ;
                    $this->get('mailer')->send($mensaje);
                }
            }

            $manager->persist($comentario);
            $manager->flush();
        }
        return $this->render(
                'MondelCuentaloBundle:Contenido:comentariosMostrar.html.twig',
                array(
                        'offset' => $offset,
                        'contenido'  => $contenido,
                        'formularios_comentarios' => array($id => $formulario->createView())
                )
        );
    }

    public function comentarioEliminarAction($id)
    {
    	$manager = $this->getDoctrine()->getEntityManager();
    	$comentario = $manager->getRepository('MondelCuentaloBundle:Comentario')->find($id);
    	
		if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
    	
    	if (!$comentario)
    		throw $this->createNotFoundException('El comentario que intentas eliminar no existe');
    	
    	$contenido = $comentario->getContenido();
        $usuario = $comentario->getUsuario();
    	
    	if ($this->get('security.context')->getToken()->getUser()->getId() == $usuario->getId()) {
    		$manager->remove($comentario);            
            // Compruebo si tiene una suscripcion a ese contenido, y borro su unico comentario
            // elimino la suscripcion tambien
            if ($contenido->getUsuario() != null && $contenido->getUsuario()->getId() != $usuario->getId()) {
                $query_builder = $manager->createQueryBuilder();
                $query_builder->add('select', 'c')
                    ->add('from', 'Mondel\CuentaloBundle\Entity\Comentario c')
                    ->add('where', 'c.usuario = ?1 and c.contenido = ?2')
                    ->setParameters(array(1 => $usuario, 2 => $contenido));
                                    
                if (count($query_builder->getQuery()->getArrayResult()) == 1) {
                    $query_builder = $manager->createQueryBuilder();
                    $query_builder->add('select', 's')
                        ->add('from', 'Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion s')
                        ->add('where', 's.usuario = ?1 and s.contenido = ?2')
                        ->setParameters(array(1 => $usuario, 2 => $contenido));
                    $suscripcion = $query_builder->getQuery()->getSingleResult();
                    
                    foreach($suscripcion->getNotificaciones() as $notificacion) {
                        $manager->remove($notificacion);
                    }

                    $manager->remove($suscripcion);
                }
            }
            $manager->flush();


            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->get('session')->setFlash('notice', 'Se ha eliminado el comentario correctamente');
            }
    	} else {
    		throw $this->createNotFoundException('El comentario que intentas eliminar no es tuyo');
    	} 
        if ($this->getRequest()->isXmlHttpRequest()) {
            return new Response('Se ha eliminado el comentario correctamente');   	   	   	
        } else {
            return $this->redirect($this->generateUrl(
        			'_contenido_pagina_mostrar',
        			array('id' => $contenido->getId())
        	));
        }
    }
    
    public function paginaMostrarAction($id)
    {
        $contenido = $this->getDoctrine()->getRepository('MondelCuentaloBundle:Contenido')->find($id);

        if (!$contenido || !$contenido->getActivo())
            throw $this->createNotFoundException('El post que intentas ver no existe o esta desactivado');

        $comentario = new Comentario();
        $formulario = $this->createForm(new ComentarioType(), $comentario);

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $usuario = $this->get('security.context')->getToken()->getUser();
            if ($usuario->estaSuscritoContenido($id)) {
                $this->getDoctrine()->getRepository('MondelCuentaloBundle:Usuario')->marcarNotificacionesComoLeidas($usuario->getId(), $id);
            }
        }

        return $this->render(
            'MondelCuentaloBundle:Contenido:paginaMostrar.html.twig',
            array(
                'contenido'  => $contenido,
                'formularios_comentarios' => array($id => $formulario->createView())
            )
        );
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
    					'formularios_comentarios' => array($id => $formulario->createView())
    			)
    	);
    }
    
    public function listarAction($categoria,$inicio,$cantidad)
    {
        $manager = $this->getDoctrine()->getEntityManager();       

        $query_builder = $manager->createQueryBuilder();
        
        if ($categoria == '0') {
	        $query_builder->add('select', 'c')
	        	->add('from', 'Mondel\CuentaloBundle\Entity\Contenido c')
	        	->add('where', 'c.activo = ?1 and c.id < ?2')
	        	->add('orderBy', 'c.fecha_creacion DESC')
	        	->setMaxResults($cantidad)
	        	->setParameters(array(1 => 1, 2 => $inicio));
        } else {
        	$query_builder->add('select', 'c')
	        	->add('from', 'Mondel\CuentaloBundle\Entity\Contenido c')
	        	->add('where', 'c.activo = ?1 and c.id < ?2 and c.categoria = ?3')
	        	->add('orderBy', 'c.fecha_creacion DESC')
	        	->setMaxResults($cantidad)
	        	->setParameters(array(
	        			1 => 1, 
	        			2 => $inicio,
	        			3 => $this->getDoctrine()->getRepository('MondelCuentaloBundle:Categoria')->find($categoria)	        			
	        			));
        }
        
        $contenidos = $query_builder->getQuery()
        	->getResult();
         
        // Creo los formularios de comentarios para cada contenido
        $formularios_comentarios = array();
        foreach ($contenidos as $contenido) {
        	$comentario = new Comentario();
        	$formulario_comentario = $this->createForm(new ComentarioType(), $comentario);
        	$formularios_comentarios[$contenido->getId()] = $formulario_comentario->createView();
        }
        
        return $this->render(
            'MondelCuentaloBundle:Contenido:listar.html.twig',
            array(
            		'contenidos' => $contenidos,
            		'formularios_comentarios' => $formularios_comentarios
            		)
        );

    }

    public function categoriaListarAction($categoria)
    {
    	$categoria = $this->getDoctrine()
    		->getRepository('MondelCuentaloBundle:Categoria')
    			->findOneBy(array('nombre' => $categoria));
    	
    	if (!$categoria)
    		throw $this->createNotFoundException('Esta categoría no existe');
    	
    	$manager = $this->getDoctrine()->getEntityManager();
    	
    	$query_builder = $manager->createQueryBuilder();
    	
    	$query_builder->add('select', 'c')
    		->add('from', 'Mondel\CuentaloBundle\Entity\Contenido c')
    		->add('where', 'c.activo = ?1 and c.categoria = ?2')
    		->add('orderBy', 'c.fecha_creacion DESC')
    		->setMaxResults(5)
    		->setParameters(array(1 => 1, 2 => $categoria));
    	
    	$contenidos = $query_builder->getQuery()
    		->getResult();
    	
    	// Creo los formularios de comentarios para cada contenido
    	$formularios_comentarios = array();
    	foreach ($contenidos as $contenido) {
    		$comentario = new Comentario();
    		$formulario_comentario = $this->createForm(new ComentarioType(), $comentario);
    		$formularios_comentarios[$contenido->getId()] = $formulario_comentario->createView();
    	}
	    	
    	// Creo el formulario para pasarle al crear de Contenido
    	$contenido = new Contenido();
    	$formulario_contenido = $this->createForm(new ContenidoType(), $contenido);
    	
    	return $this->render(
    			'MondelCuentaloBundle:Default:inicio.html.twig',
    			array(
                        'pagina_titulo' => $categoria->getNombre(),
    					'contenidos'    => $contenidos,
    					'form'          => $formulario_contenido->createView(),
    					'formularios_comentarios' => $formularios_comentarios,
    					'cid'			=> $categoria->getId() 
    			)
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

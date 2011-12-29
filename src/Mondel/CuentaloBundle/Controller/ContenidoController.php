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
            
            if ($contenido->getCategoria() == null) {
                $this->get('session')->setFlash('error', 'debe seleccionar una categoria.');
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

            $manager->persist($comentario);
            $manager->flush();
        }
        return $this->redirect($this->generateUrl(
                '_contenido_pagina_mostrar',
                array('id' => $id)
        ));
    }

    public function comentarioEliminarAction($id)
    {
    	$manager = $this->getDoctrine()->getEntityManager();
    	$comentario = $manager->getRepository('MondelCuentaloBundle:Comentario')->find($id);
    	
		if (false === $this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
    	
    	if (!$comentario)
    		throw $this->createNotFoundException('El comentario que intentas eliminar no existe');
    	
    	$idContenido = $comentario->getContenido()->getId();    	
    	
    	if ($this->get('security.context')->getToken()->getUser()->getId() == $comentario->getUsuario()->getId()) {
    		$manager->remove($comentario);
    		$manager->flush();
    		$this->get('session')->setFlash('notice', 'Se ha eliminado el comentario correctamente');
    	} else {
    		throw $this->createNotFoundException('El comentario que intentas eliminar no es tuyo');
    	}    	   	   	
    	return $this->redirect($this->generateUrl(
    			'_contenido_pagina_mostrar',
    			array('id' => $idContenido)
    	));        	
    }
    
    public function paginaMostrarAction($id)
    {
        $contenido = $this->getDoctrine()->getRepository('MondelCuentaloBundle:Contenido')->find($id);

        if (!$contenido || !$contenido->getActivo())
            throw $this->createNotFoundException('El post que intentas ver no existe o esta desactivado');

        $comentario = new Comentario();
        $formulario = $this->createForm(new ComentarioType(), $comentario);

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

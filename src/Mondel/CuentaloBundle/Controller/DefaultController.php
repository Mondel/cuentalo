<?php

namespace Mondel\CuentaloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Mondel\CuentaloBundle\Entity\Contenido,
    Mondel\CuentaloBundle\Entity\Comentario;
use Mondel\CuentaloBundle\Form\Type\ContenidoType,
    Mondel\CuentaloBundle\Form\Type\ComentarioType;

class DefaultController extends Controller
{
    public function inicioAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();

        $manager = $this->getDoctrine()->getEntityManager();
        
        $query_builder = $manager->createQueryBuilder();
        
        $query_builder->add('select', 'c')
	        ->add('from', 'Mondel\CuentaloBundle\Entity\Contenido c')
	        ->add('where', 'c.activo = ?1')
	        ->add('orderBy', 'c.fecha_creacion DESC')        
	        ->setMaxResults(5)
	        ->setParameters(array(1 => 1));
        
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
                	'cid'		=> '0'
                )
        );
    }
    
    public function paginaAction($pagina)
    {
    	return $this->render(
    			'MondelCuentaloBundle:Paginas:' . $pagina . '.html.twig');
    }

	public function contactoAction()
	{	
		$peticion = $this->getRequest();
		  
		$form = $this->createFormBuilder()
			->add('nombre', 'text')
			->add('email', 'email')
			->add('mensaje', 'textarea')
			->add('tipo', 'choice', array(
					'choices'   => array('0' => 'Consulta', '1' => 'Reportar Error', '2' => 'Otro'),
					'required'  => true,
			))
			->getForm();
		
		if ($peticion->getMethod() == 'POST') {
			$form->bindRequest($peticion);
		
			if ($form->isValid()) {
				
				$form_data = $form->getData();
				
				$tipos = array('0' => 'Consulta', '1' => 'Reportar Error', '2' => 'Otro');
				$email_to = array('0' => 'info@cuentalo.com.uy', '1' => 'bugs@cuentalo.com.uy', '2' => 'info@cuentalo.com.uy');
				
				$form_data_enviar = "Nombre: " . $form_data['nombre'] . "\n"
					. "Email: " . $form_data['email'] . "\n"
					. "Tipo: " . $tipos[$form_data['tipo']] . "\n"
					. "Mensaje: " . $form_data['mensaje'];
				
				$mensaje = \Swift_Message::newInstance()
					->setSubject('Cuentalo: email de activación')
					->setFrom('registros@cuentalo.com.uy')
					->setTo('nico.racing.99@gmail.com')
					->setBody('hola ' . $form_data_enviar);
				
				$this->get('mailer')->send($mensaje);
		
				$this->get('session')->setFlash('notice', 'Se ha enviado su mensaje. Gracias');
				return $this->redirect($this->generateUrl('_inicio'));
			}
		}
		
		return $this->render(
				'MondelCuentaloBundle:Default:contacto.html.twig',
				array('form' => $form->createView())
		);
	}
}

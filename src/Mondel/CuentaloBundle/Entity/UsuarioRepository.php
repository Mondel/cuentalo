<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UsuarioRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsuarioRepository extends EntityRepository
{

    public function marcarNotificacionesComoLeidas($idUsuario, $idContenido)
    {
    	$usuario = $this->_em->getRepository('MondelCuentaloBundle:Usuario')
    		->find($idUsuario);
    	$contenido = $this->_em->getRepository('MondelCuentaloBundle:Contenido')
    		->find($idContenido);

		$query = $this->_em->createQuery("SELECT ucs FROM MondelCuentaloBundle:UsuarioContenidoSuscripcion ucs WHERE ucs.usuario = :usuario AND ucs.contenido = :contenido")
			->setParameter('usuario', $usuario)
			->setParameter('contenido', $contenido);			
		$suscripcion = $query->getSingleResult();

		if ($suscripcion != null) {
		    foreach ($suscripcion->getNotificaciones() as $notificacion) {		    	
		    	$notificacion->setLeida(true);	    	
		    }
		    $this->_em->flush();
		}
    }

}
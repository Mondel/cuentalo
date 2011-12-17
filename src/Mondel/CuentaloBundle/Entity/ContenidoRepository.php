<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ContenidoRepository extends EntityRepository
{
	public function getLastComment($idContenido)
	{	
		return $this->getEntityManager()
			->createQuery('SELECT c FROM MondelCuentaloBundle:Comentario c WHERE c.contenido = ?1 ORDER BY c.fecha_creacion DESC')
			->setParameter(1, $idContenido)
			->getResult();
	}    
}
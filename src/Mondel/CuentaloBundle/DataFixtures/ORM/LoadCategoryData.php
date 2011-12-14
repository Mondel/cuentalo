<?php

namespace Mondel\CuentaloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mondel\CuentaloBundle\Entity\Categoria;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
    	$nombre_categorias = array('Amor', 'Anecdota', 'Anoche', 
    			'Chiste', 'Futbol', 'Mensaje', 'Video', 'Secreto');
    	
    	foreach ($nombre_categorias as $nombre_categoria) {
			$categoria = new Categoria();
			$categoria->setNombre($nombre_categoria);
			$categoria->setActivo(true);
			$manager->persist($categoria);
    	}
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
    
}
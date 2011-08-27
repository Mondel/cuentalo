<?php
namespace Mondel\CuentaloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mondel\CuentaloBundle\Entity\Contenido;

class LoadContenidoData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load($manager)
    {
        $texto = 'Este es un contenido de prueba, cualquier dato que contenga'
            . ' es puro invento.';
        $titulo = 'Este es el titulo del contenido';
        $tipos = array('a', 's', 'm');
        
        foreach (range(1, 100) as $i) {
            $contenido = new Contenido();
            $contenido->setIp('190.134.12.0');
            $contenido->setTexto($texto);
            $contenido->setTipo($tipos[rand(0, count($tipos)-1)]);
            $contenido->setTitulo($titulo);
            $contenido->setUsuario(
                    $manager->merge($this->getReference('usuario'.rand(1,100)))
            );
            $manager->persist($contenido);
        }
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
    
}
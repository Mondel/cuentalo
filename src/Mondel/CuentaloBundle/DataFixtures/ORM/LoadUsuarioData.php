<?php

namespace Mondel\CuentaloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mondel\CuentaloBundle\Entity\Usuario;

class LoadUsuarioData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {        
        $nombres = array('Adán', 'Adolfo', 'Agustin', 'Albert', 'Alberto',
            'Alejandro', 'Andrés', 'Antonio', 'Ariel', 'Benjamin', 'Bernardo',
            'Carles', 'Carlos', 'Cayetano', 'César', 'Cristian', 'Daniel',
            'David', 'Diego', 'Dimas', 'Eduardo', 'Eneko', 'Esteban', 'Fernando',
            'Francisco', 'Gonzalo', 'Gregorio', 'Guillermo', 'Haritz', 'Iago',
            'Ignacio', 'Iker', 'Isaïes', 'Isis', 'Iván', 'Jacob', 'Javier',
            'Joan', 'Jordi', 'Jorge', 'Jose', 'Juan', 'Kevin', 'Luis', 'Marc',
            'Marta', 'Miguel', 'Moisés', 'Oriol', 'Oscar', 'Pablo', 'Pedro',
            'Pere', 'Rafael', 'Raúl', 'Rebeca', 'Rosa', 'Rubén', 'Salvador',
            'Santiago', 'Sergio', 'Susana', 'Verónica', 'Vicente', 'Víctor',
            'Victoria', 'Vidal');

        $apellidos = array('García', 'Fernández', 'González', 'Rodríguez',
            'López', 'Martínez', 'Sánchez', 'Pérez', 'Martín', 'Gómez',
            'Jiménez', 'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Álvarez', 'Muñoz',
            'Romero', 'Alonso', 'Gutiérrez', 'Navarro', 'Torres', 'Domínguez',
            'Vázquez', 'Gil', 'Ramos', 'Serrano', 'Blanco', 'Ramírez', 'Molina',
            'Suárez', 'Ortega', 'Delgado', 'Morales', 'Castro', 'Rubio', 'Ortíz',
            'Marín', 'Sanz', 'Iglesias', 'Núñez', 'Garrido', 'Cortés', 'Medina',
            'Santos', 'Lozano', 'Cano', 'Castillo', 'Gerrero', 'Prieto');

        $sexos = array('m', 'f');

        $factory = $this->container->get('security.encoder_factory');

        foreach (range(1, 100) as $i) {
            $usuario = new Usuario();

            $usuario->setNombre($nombres[rand(0, count($nombres) - 1)]);
            $usuario->setApellido($apellidos[rand(0, count($apellidos) - 1)]);
            $usuario->setEmail('usuario' . $i . '@cuentalo.com.uy');
            $usuario->setSexo($sexos[rand(0, count($sexos) - 1)]);

            $encoder = $factory->getEncoder($usuario);
            $contrasenia = $encoder->encodePassword(
                    'usuario' . $i, $usuario->getSalt()
            );
            $usuario->setContrasenia($contrasenia);

            $this->addReference('usuario'.$i, $usuario);
            
            $manager->persist($usuario);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
    
}
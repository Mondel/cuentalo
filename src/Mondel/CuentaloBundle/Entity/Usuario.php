<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Mondel\CuentaloBundle\Entity\Usuario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\CuentaloBundle\Entity\UsuarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Usuario implements UserInterface
{
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @Assert\NotBlank()     
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string $apellido
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @Assert\NotBlank()
     * @ORM\Column(name="apellido", type="string", length=50)
     */
    private $apellido;

    /**
     * @var string $email
     *
     * @Assert\Email()
     * @Assert\MaxLength(50)
     * @Assert\NotBlank()
     * @ORM\Column(name="email", type="string", length=50)
     */
    private $email;

    /**
     * @var string $sexo
     *
     * @Assert\Choice({"m", "f"})
     * @ORM\Column(name="sexo", type="string", length=1, unique="true")
     */
    private $sexo;

    /**
     * @var date $fecha_creacion
     *
     * @ORM\Column(name="fecha_creacion", type="date")
     */
    private $fecha_creacion;

    /**
     * @var date $fecha_actualizacion
     *
     * @ORM\Column(name="fecha_actualizacion", type="date")
     */
    private $fecha_actualizacion;

    /**
     * @var string $contrasenia
     *
     * @Assert\MaxLength(10)
     * @Assert\MinLength(6)
     * @Assert\NotBlank()
     * @ORM\Column(name="contrasenia", type="string", length=255)
     */
    private $contrasenia;

    /**
     * @ORM\OneToMany(targetEntity="Contenido", mappedBy="usuario")
     */
    private $contenidos;

    /**
     * @ORM\OneToMany(targetEntity="Voto", mappedBy="usuario")
     */
    private $votos;    
    
    /**
     * @ORM\prePersist
     */
    public function setFechaCreacion()
    {
        $this->fecha_creacion = new \DateTime();
        $this->fecha_actualizacion = $this->getFechaCreacion();
    }
    
    /**
     * @ORM\postUpdate
     */
    public function setFechaActualizacion()
    {
        $this->fecha_actualizacion = new \Date();
    }
    
    /*
     * Implements UserInterface
     */
    public function equals(UserInterface $user) {
        return $user->getUsername() == $this->getUsername();
    }

    public function eraseCredentials() {
        
    }

    public function getPassword() {
        return $this->getContrasenia();
    }

    public function getRoles() {
        return array('ROLE_USER');
    }

    public function getSalt() {
        return md5(time());
    }

    public function getUsername() {
        return $this->getEmail();
    }
    /*
     * Fin Implements UserInterface
     */
    
    /*
     * Fin mis propiedades
     */
    
    public function __construct()
    {
        $this->contenidos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->votos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Get fecha_creacion
     *
     * @return date 
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    /**
     * Get fecha_actualizacion
     *
     * @return date 
     */
    public function getFechaActualizacion()
    {
        return $this->fecha_actualizacion;
    }

    /**
     * Set contrasenia
     *
     * @param string $contrasenia
     */
    public function setContrasenia($contrasenia)
    {
        $this->contrasenia = $contrasenia;
    }

    /**
     * Get contrasenia
     *
     * @return string 
     */
    public function getContrasenia()
    {
        return $this->contrasenia;
    }

    /**
     * Add contenidos
     *
     * @param Mondel\CuentaloBundle\Entity\Contenido $contenidos
     */
    public function addContenidos(\Mondel\CuentaloBundle\Entity\Contenido $contenidos)
    {
        $this->contenidos[] = $contenidos;
    }

    /**
     * Get contenidos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getContenidos()
    {
        return $this->contenidos;
    }

    /**
     * Add votos
     *
     * @param Mondel\CuentaloBundle\Entity\Voto $votos
     */
    public function addVotos(\Mondel\CuentaloBundle\Entity\Voto $votos)
    {
        $this->votos[] = $votos;
    }

    /**
     * Get votos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getVotos()
    {
        return $this->votos;
    }
    
}
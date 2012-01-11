<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Mondel\CuentaloBundle\Entity\Usuario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\CuentaloBundle\Entity\UsuarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Usuario implements AdvancedUserInterface
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
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @var string $apellido
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @ORM\Column(name="apellido", type="string", length=50, nullable=true)
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
     * @var string $email_alternativo
     *
     * @Assert\Email()
     * @Assert\MaxLength(50)     
     * @ORM\Column(name="email_alternativo", type="string", length=50, nullable=true)
     */
    private $email_alternativo;

    /**
     * @var string $nick
     *
     * @Assert\MinLength(3)
     * @Assert\MaxLength(50)
     * @Assert\NotBlank()
     * @ORM\Column(name="nick", type="string", length=50)
     */
    private $nick;

    /**
     * @var string $sexo
     *
     * @Assert\Choice({"m", "f", "i"})
     * @ORM\Column(name="sexo", type="string", length=1)
     */
    private $sexo;

    /**
     * @var date $fecha_nacimiento
     *
     * @ORM\Column(name="fecha_nacimiento", type="date", nullable=true)
     */
    private $fecha_nacimiento;

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
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

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
     * @var boolean $activo
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var boolean $recibe_noticias
     *
     * @ORM\Column(name="recibe_noticias", type="boolean", nullable=true)
     */
    private $recibe_noticias;

    /**
     * @var boolean $recibe_notificaciones
     *
     * @ORM\Column(name="recibe_notificaciones", type="boolean", nullable=true)
     */
    private $recibe_notificaciones;

    /**
     * @var boolean $admin
     *
     * @ORM\Column(name="admin", type="boolean")
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity="Contenido", mappedBy="usuario")
     */
    private $contenidos;
    
    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="usuario")
     */
    private $comentarios;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioContenidoSuscripciones", mappedBy="usuario")
     */
    private $contenido_suscripciones;

    /*
     * Implements AdvancedUserInterface
     */
    public function equals(UserInterface $user)
    {
        return md5($user->getUsername()) == md5($this->getUsername());
    }

    public function eraseCredentials()
    {

    }

    public function getPassword()
    {
        return $this->getContrasenia();
    }

    public function getRoles()
    {
        if ($this->admin)
            return array('ROLE_ADMIN');
        else
            return array('ROLE_USER');
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->activo;
    }
    /*
     * Fin Implements AdvancedUserInterface
     */

    /**
     * @ORM\prePersist
     */
    public function setFechaCreacion()
    {
        $this->fecha_creacion = new \DateTime();
        $this->fecha_actualizacion = $this->getFechaCreacion();
    }

    /**
     * @ORM\prePersist
     */
    public function setEstadoInactivo()
    {
        $this->activo = false;
    }

    /**
     * @ORM\prePersist
     */
    public function setPermisos()
    {
        $this->admin = false;
    }

    /**
     * @ORM\postUpdate
     */
    public function setFechaActualizacion()
    {
        $this->fecha_actualizacion = new \DateTime();
    }   
    
    /*
     * Fin mis propiedades
     */
    public function __construct()
    {
        $this->contenidos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
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
     * @return Usuario
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
        return $this;
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
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
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
     * Set email_alternativo
     *
     * @param string $emailAlternativo
     * @return Usuario
     */
    public function setEmailAlternativo($emailAlternativo)
    {
        $this->email_alternativo = $emailAlternativo;
        return $this;
    }

    /**
     * Get email_alternativo
     *
     * @return string
     */
    public function getEmailAlternativo()
    {
        return $this->email_alternativo;
    }

    /**
     * Set nick
     *
     * @param string $nick
     * @return Usuario
     */
    public function setNick($nick)
    {
        $this->nick = $nick;
        return $this;
    }

    /**
     * Get nick
     *
     * @return string
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     * @return Usuario
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
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
     * Set fecha_nacimiento
     *
     * @param date $fechaNacimiento
     * @return Usuario
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fecha_nacimiento = $fechaNacimiento;
        return $this;
    }

    /**
     * Get fecha_nacimiento
     *
     * @return date
     */
    public function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
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
     * Set salt
     *
     * @param string $salt
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Set contrasenia
     *
     * @param string $contrasenia
     * @return Usuario
     */
    public function setContrasenia($contrasenia)
    {
        $this->contrasenia = $contrasenia;
        return $this;
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
     * Set activo
     *
     * @param boolean $activo
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set recibe_noticias
     *
     * @param boolean $recibeNoticias
     * @return Usuario
     */
    public function setRecibeNoticias($recibeNoticias)
    {
        $this->recibe_noticias = $recibeNoticias;
        return $this;
    }

    /**
     * Get recibe_noticias
     *
     * @return boolean
     */
    public function getRecibeNoticias()
    {
        return $this->recibe_noticias;
    }

    /**
     * Set recibe_notificaciones
     *
     * @param boolean $recibeNotificaciones
     * @return Usuario
     */
    public function setRecibeNotificaciones($recibeNotificaciones)
    {
        $this->recibe_notificaciones = $recibeNotificaciones;
        return $this;
    }

    /**
     * Get recibe_notificaciones
     *
     * @return boolean
     */
    public function getRecibeNotificaciones()
    {
        return $this->recibe_notificaciones;
    }

    /**
     * Add contenidos
     *
     * @param Mondel\CuentaloBundle\Entity\Contenido $contenidos
     */
    public function addContenido(\Mondel\CuentaloBundle\Entity\Contenido $contenidos)
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
     * Set admin
     *
     * @param boolean $admin
     * @return Usuario
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     * Get admin
     *
     * @return boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Add comentarios
     *
     * @param Mondel\CuentaloBundle\Entity\Comentario $comentarios
     */
    public function addComentario(\Mondel\CuentaloBundle\Entity\Comentario $comentarios)
    {
        $this->comentarios[] = $comentarios;
    }

    /**
     * Get comentarios
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }
}
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
    protected $id;

    /**
     * @var string $nombre
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     */
    protected $nombre;

    /**
     * @var string $apellido
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @ORM\Column(name="apellido", type="string", length=50, nullable=true)
     */
    protected $apellido;

    /**
     * @var string $email
     *
     * @Assert\Email()
     * @Assert\MaxLength(50)
     * @Assert\NotBlank()
     * @ORM\Column(name="email", type="string", length=50)
     */
    protected $email;

    /**
     * @var string $email_alternativo
     *
     * @Assert\Email()
     * @Assert\MaxLength(50)     
     * @ORM\Column(name="email_alternativo", type="string", length=50, nullable=true)
     */
    protected $email_alternativo;

    /**
     * @var string $nick
     *
     * @Assert\MinLength(3)
     * @Assert\MaxLength(50)
     * @Assert\NotBlank()
     * @ORM\Column(name="nick", type="string", length=50)
     */
    protected $nick;

    /**
     * @var string $sexo
     *
     * @Assert\Choice({"m", "f", "i"})
     * @ORM\Column(name="sexo", type="string", length=1)
     */
    protected $sexo;

    /**
     * @var date $fecha_nacimiento
     *
     * @ORM\Column(name="fecha_nacimiento", type="date", nullable=true)
     */
    protected $fecha_nacimiento;

    /**
     * @var date $fecha_creacion
     *
     * @ORM\Column(name="fecha_creacion", type="date")
     */
    protected $fecha_creacion;

    /**
     * @var date $fecha_actualizacion
     *
     * @ORM\Column(name="fecha_actualizacion", type="date")
     */
    protected $fecha_actualizacion;

    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var string $contrasenia
     *
     * @Assert\MaxLength(10)
     * @Assert\MinLength(6)
     * @Assert\NotBlank()
     * @ORM\Column(name="contrasenia", type="string", length=255)
     */
    protected $contrasenia;

    /**
     * @var boolean $activo
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    protected $activo;

    /**
     * @var boolean $recibe_noticias
     *
     * @ORM\Column(name="recibe_noticias", type="boolean", nullable=true)
     */
    protected $recibe_noticias;

    /**
     * @var boolean $recibe_notificaciones
     *
     * @ORM\Column(name="recibe_notificaciones", type="boolean", nullable=true)
     */
    protected $recibe_notificaciones;

    /**
     * @var boolean $admin
     *
     * @ORM\Column(name="admin", type="boolean")
     */
    protected $admin;

    /**
     * @ORM\OneToMany(targetEntity="Contenido", mappedBy="usuario")
     */
    protected $contenidos;
    
    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="usuario")
     */
    protected $comentarios;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioContenidoSuscripcion", mappedBy="usuario")
     */
    protected $contenido_suscripciones;

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
    
    /**
     *  Devuelve true si tiene notificaciones sin leer 
     */
    public function tieneNotificacionesSinLeer()
    {
        foreach ($this->getContenidoSuscripciones() as $suscripcion) {
            foreach ($suscripcion->getNotificaciones() as $notificacion) {
                if (!$notificacion->getLeida()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Devuelve un array con las notificaciones del usuario
     */    
    public function obtenerNotificaciones()
    {
        $notificaciones = array();

        foreach ($this->getContenidoSuscripciones() as $suscripcion) {
            foreach ($suscripcion->getNotificaciones() as $notificacion) {
                array_push($notificaciones, $notificacion);
            }
        }
        
        usort($notificaciones, function($a, $b) {
            if ($a->getFechaCreacion() == $b->getFechaCreacion()) {
                return 0;
            }
            return ($a->getFechaCreacion() > $b->getFechaCreacion()) ? -1 : 1;
        });

        return $notificaciones;
    }

    /*
     * Fin mis propiedades
     */     
    public function __construct()
    {
        $this->contenidos = new \Doctrine\Common\Collections\ArrayCollection();
    $this->comentarios = new \Doctrine\Common\Collections\ArrayCollection();
    $this->contenido_suscripciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email_alternativo
     *
     * @param string $emailAlternativo
     */
    public function setEmailAlternativo($emailAlternativo)
    {
        $this->email_alternativo = $emailAlternativo;
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
     */
    public function setNick($nick)
    {
        $this->nick = $nick;
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
     * Set fecha_nacimiento
     *
     * @param date $fechaNacimiento
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fecha_nacimiento = $fechaNacimiento;
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
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
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
     * Set activo
     *
     * @param boolean $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
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
     */
    public function setRecibeNoticias($recibeNoticias)
    {
        $this->recibe_noticias = $recibeNoticias;
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
     */
    public function setRecibeNotificaciones($recibeNotificaciones)
    {
        $this->recibe_notificaciones = $recibeNotificaciones;
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
     * Set admin
     *
     * @param boolean $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
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

    /**
     * Add contenido_suscripciones
     *
     * @param Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion $contenidoSuscripciones
     */
    public function addUsuarioContenidoSuscripcion(\Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion $contenidoSuscripciones)
    {
        $this->contenido_suscripciones[] = $contenidoSuscripciones;
    }

    /**
     * Get contenido_suscripciones
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getContenidoSuscripciones()
    {
        return $this->contenido_suscripciones;
    }
}
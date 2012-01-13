<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcionRepository")
 */
class UsuarioContenidoSuscripcion
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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="contenidos_suscrito")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    protected $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="Contenido", inversedBy="usuarios_suscritos")
     * @ORM\JoinColumn(name="contenido_id", referencedColumnName="id")
     */
    protected $contenido;

     /**
     * @ORM\OneToMany(targetEntity="Notificacion", mappedBy="usuario_contenido_suscripcion")
     */
    protected $notificaciones;

    /*
     * Fin mis propiedades
     */    

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
     * Set usuario
     *
     * @param Mondel\CuentaloBundle\Entity\Usuario $usuario
     */
    public function setUsuario(\Mondel\CuentaloBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Get usuario
     *
     * @return Mondel\CuentaloBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set contenido
     *
     * @param Mondel\CuentaloBundle\Entity\Contenido $contenido
     */
    public function setContenido(\Mondel\CuentaloBundle\Entity\Contenido $contenido)
    {
        $this->contenido = $contenido;
    }

    /**
     * Get contenido
     *
     * @return Mondel\CuentaloBundle\Entity\Contenido 
     */
    public function getContenido()
    {
        return $this->contenido;
    }
    public function __construct()
    {
        $this->notificaciones = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add notificaciones
     *
     * @param Mondel\CuentaloBundle\Entity\Notificacion $notificaciones
     */
    public function addNotificacion(\Mondel\CuentaloBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones[] = $notificaciones;
    }

    /**
     * Get notificaciones
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getNotificaciones()
    {
        return $this->notificaciones;
    }
}
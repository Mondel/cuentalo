<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\CuentaloBundle\Entity\Mensaje
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Mensaje
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
     * @var string $ip
     *
     * @Assert\MaxLength(20)
     * @ORM\Column(name="ip", type="string", length=20)
     */
    protected $ip;

    /**
     * @var string $asunto
     *     
     * @Assert\MaxLength(255)
     * @ORM\Column(name="asunto", type="string", length=255)
     */
    protected $asunto;

    /**
     * @var string $texto
     *     
     * @ORM\Column(name="texto", type="text")
     */
    protected $texto;

    /**
     * @var datetime $fecha_creacion
     *
     * @ORM\Column(name="fecha_creacion", type="datetime")
     */
    protected $fecha_creacion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="mensajes_enviados")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    protected $usuario_remitente;

    /**
     * @ORM\ManyToMany(targetEntity="Usuario", inversedBy="mensajes_recibidos")
     * @ORM\JoinTable(name="MensajeUsuarioDestinatario",
     *      joinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mensaje_id", referencedColumnName="id")}
     *      )
     */
    protected $usuarios_destinatarios;

    /**
     * @ORM\prePersist
     */
    public function setFechaCreacion()
    {
        $this->fecha_creacion = new \DateTime();
    }

    /*
     * Fin mis propiedades
     */
    public function __construct()
    {
        $this->usuarios_destinatarios = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get fecha_creacion
     *
     * @return datetime 
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    /**
     * Set usuario_remitente
     *
     * @param Mondel\CuentaloBundle\Entity\Usuario $usuarioRemitente
     */
    public function setUsuarioRemitente(\Mondel\CuentaloBundle\Entity\Usuario $usuarioRemitente)
    {
        $this->usuario_remitente = $usuarioRemitente;
    }

    /**
     * Get usuario_remitente
     *
     * @return Mondel\CuentaloBundle\Entity\Usuario 
     */
    public function getUsuarioRemitente()
    {
        return $this->usuario_remitente;
    }

    /**
     * Add usuarios_destinatarios
     *
     * @param Mondel\CuentaloBundle\Entity\Usuario $usuariosDestinatarios
     */
    public function addUsuario(\Mondel\CuentaloBundle\Entity\Usuario $usuariosDestinatarios)
    {
        $this->usuarios_destinatarios[] = $usuariosDestinatarios;
    }

    /**
     * Get usuarios_destinatarios
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsuariosDestinatarios()
    {
        return $this->usuarios_destinatarios;
    }
}
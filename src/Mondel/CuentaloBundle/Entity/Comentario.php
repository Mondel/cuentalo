<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Mondel\CuentaloBundle\Helpers\NetworkHelper,
    Mondel\CuentaloBundle\Helpers\StringHelper;


/**
 * Mondel\CuentaloBundle\Entity\Comentario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\CuentaloBundle\Entity\ComentarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comentario
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
     * @var string $texto
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="texto", type="text")
     */
    private $texto;

    /**
     * @var string $ip
     *
     * @Assert\MaxLength(20)
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;

    /**
     * @var string $pais
     *
     * @ORM\Column(name="pais", type="string", length=50)
     */
    private $pais;

    /**
     * @var boolean $activo
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var datetime $fecha_creacion
     *
     * @ORM\Column(name="fecha_creacion", type="datetime")
     */
    private $fecha_creacion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="comentarios")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="Contenido", inversedBy="comentarios")
     * @ORM\JoinColumn(name="contenido_id", referencedColumnName="id")
     */
    private $contenido;

    /**
     * @ORM\prePersist
     */
    public function setPais()
    {
        $this->pais = NetworkHelper::getCountryNameByIp($this->getIp());
    }

    /**
     * @ORM\prePersist
     */
    public function setFechaCreacion()
    {
        $this->fecha_creacion = new \DateTime();
    }

    /**
     * @ORM\prePersist
     */
    public function setEstadoActivo()
    {
        $this->activo = true;
    }

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
     * Set texto
     *
     * @param text $texto
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    }

    /**
     * Get texto
     *
     * @return text 
     */
    public function getTexto()
    {
        return $this->texto;
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
     * Get pais
     *
     * @return string 
     */
    public function getPais()
    {
        return $this->pais;
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
     * Get fecha_creacion
     *
     * @return datetime 
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
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
}
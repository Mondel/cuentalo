<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Mondel\CuentaloBundle\Helpers\NetworkHelper,
    Mondel\CuentaloBundle\Helpers\StringHelper;

/**
 * Mondel\CuentaloBundle\Entity\Contenido
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\CuentaloBundle\Entity\ContenidoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contenido
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
     * @Assert\MaxLength(555)
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
     * @var string $sexo
     *
     
     * @ORM\Column(name="sexo", type="string", length=1, nullable=true)
     */
    private $sexo;

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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="contenidos")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

     /**
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="contenidos")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;

    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="contenido")
     */
    private $comentarios;

    /**
     * @ORM\OneToMany(targetEntity="Voto", mappedBy="voto")
     */
    private $votos;

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
     * Fin mis propiedes
     */


    public function __construct()
    {
        $this->comentarios = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set categoria
     *
     * @param Mondel\CuentaloBundle\Entity\Categoria $categoria
     */
    public function setCategoria(\Mondel\CuentaloBundle\Entity\Categoria $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     * Get categoria
     *
     * @return Mondel\CuentaloBundle\Entity\Categoria 
     */
    public function getCategoria()
    {
        return $this->categoria;
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
     * Add votos
     *
     * @param Mondel\CuentaloBundle\Entity\Voto $votos
     */
    public function addVoto(\Mondel\CuentaloBundle\Entity\Voto $votos)
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
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
     * @var string $titulo
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(5)
     * @Assert\NotBlank()     
     * @ORM\Column(name="titulo", type="string", length=50)
     */
    private $titulo;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=100)     
     */
    private $slug;
    
    /**
     * @var string $tipo
     *
     * @Assert\Choice({"m", "a", "s"})
     * @ORM\Column(name="tipo", type="string", length=1)
     */
    private $tipo;

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
     * @var string pais
     *
     * @ORM\Column(name="pais", type="string", length=50)
     */
    private $pais;
    
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
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="contenido")
     */
    private $comentarios;
    
    /**
     * @ORM\OneToMany(targetEntity="Voto", mappedBy="contenido")
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
    public function setPais()
    {
        $this->pais = NetworkHelper::getCountryNameByIp($this->getIp());
    }
    
    /**
     * @ORM\prePersist
     */
    public function setSlug()
    {
        $this->slug = StringHelper::slugify($this->getTitulo());
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
     * Set titulo
     *
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
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
     * Get fecha_creacion
     *
     * @return date 
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
     * Add comentarios
     *
     * @param Mondel\CuentaloBundle\Entity\Comentario $comentarios
     */
    public function addComentarios(\Mondel\CuentaloBundle\Entity\Comentario $comentarios)
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
     * Add votos
     *
     * @param Mondel\CuentaloBundle\Entity\Voto $votos
     */
    public function addVoto(\Mondel\CuentaloBundle\Entity\Voto $votos)
    {
        $this->votos[] = $votos;
    }
}
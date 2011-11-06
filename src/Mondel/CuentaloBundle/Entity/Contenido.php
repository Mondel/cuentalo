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
     * @Assert\MinLength(50)
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
     * @var string pais
     *
     * @ORM\Column(name="pais", type="string", length=50)
     */
    private $pais;

    /**
     * @var string $sexo
     *
     * @Assert\Choice({"m", "f"})
     * @ORM\Column(name="sexo", type="string", length=1, nullable=true)
     */
    private $sexo;

    /**
     * @var string $estado
     *
     * @Assert\Choice({"a", "p", "r"})
     * @ORM\Column(name="estado", type="string", length=1)
     */
    private $estado;

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
    public function setEstadoPendiente()
    {
        $this->estado = "p";
    }

    /*
     * Fin mis propiedes
     */

    public function __construct()
    {
        $this->comentarios = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Contenido
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
        return $this;
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
     * @return Contenido
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
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
     * Set sexo
     *
     * @param string $sexo
     * @return Contenido
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
     * Set estado
     *
     * @param string $estado
     * @return Contenido
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
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
     * @return Contenido
     */
    public function setUsuario(\Mondel\CuentaloBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;
        return $this;
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
     * @return Contenido
     */
    public function setCategoria(\Mondel\CuentaloBundle\Entity\Categoria $categoria)
    {
        $this->categoria = $categoria;
        return $this;
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
}
<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\CuentaloBundle\Entity\Notificacion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\CuentaloBundle\Entity\NotificacionRepository")
 */
class Notificacion
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
     * @var string texto
     *
     * @ORM\Column(name="texto", type="string", length=255)
     */
    private $texto;

    /**
     * @var boolean $leida
     *
     * @ORM\Column(name="leida", type="boolean")
     */
    private $leida;

    /**
     * @ORM\ManyToOne(targetEntity="UsuarioContenidoSuscripcion", inversedBy="notificacion")
     * @ORM\JoinColumn(name="usuario_contenido_suscripcion_id", referencedColumnName="id")
     */
    private $usuario_contenido_suscripcion;

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
     * @param string $texto
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set leida
     *
     * @param boolean $leida
     */
    public function setLeida($leida)
    {
        $this->leida = $leida;
    }

    /**
     * Get leida
     *
     * @return boolean 
     */
    public function getLeida()
    {
        return $this->leida;
    }

    /**
     * Set usuario_contenido_suscripcion
     *
     * @param Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion $usuarioContenidoSuscripcion
     */
    public function setUsuarioContenidoSuscripcion(\Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion $usuarioContenidoSuscripcion)
    {
        $this->usuario_contenido_suscripcion = $usuarioContenidoSuscripcion;
    }

    /**
     * Get usuario_contenido_suscripcion
     *
     * @return Mondel\CuentaloBundle\Entity\UsuarioContenidoSuscripcion 
     */
    public function getUsuarioContenidoSuscripcion()
    {
        return $this->usuario_contenido_suscripcion;
    }
}
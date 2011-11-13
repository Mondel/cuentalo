<?php

namespace Mondel\CuentaloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mondel\CuentaloBundle\Entity\UsuarioRegistro
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UsuarioRegistro
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
     * @var string $contrasenia
     *
     * @ORM\Column(name="contrasenia", type="string", length=255)
     */
    private $contrasenia;

    /**
     * @var datetime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=50)
     */
    private $ip;

    /**
     * @var boolean $correcto
     *
     * @ORM\Column(name="correcto", type="boolean")
     */
    private $correcto;


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
     * Set contrasenia
     *
     * @param string $contrasenia
     * @return UsuarioRegistro
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
     * Set fecha
     *
     * @param datetime $fecha
     * @return UsuarioRegistro
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
        return $this;
    }

    /**
     * Get fecha
     *
     * @return datetime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return UsuarioRegistro
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
     * Set ip
     *
     * @param string $ip
     * @return UsuarioRegistro
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
     * Set correcto
     *
     * @param boolean $correcto
     * @return UsuarioRegistro
     */
    public function setCorrecto($correcto)
    {
        $this->correcto = $correcto;
        return $this;
    }

    /**
     * Get correcto
     *
     * @return boolean 
     */
    public function getCorrecto()
    {
        return $this->correcto;
    }
}
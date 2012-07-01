<?php

namespace Mondel\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\UserBundle\Entity\Notification
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Notification
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
     * @var string text
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    protected $text;

    /**
     * @var boolean $is_read
     *
     * @ORM\Column(name="is_read", type="boolean")
     */
    protected $is_read;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserPostSuscription", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_post_suscription_id", referencedColumnName="id")
     */
    protected $user_post_suscription;

    public function __construct()
    {
        $this->is_read = false;
        $this->created_at = new \DateTime();
    }

    public function isRead() {
        return $this->getIsRead();
    }

    /*
     * End custom properties
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
     * Set text
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set is_read
     *
     * @param boolean $isRead
     */
    public function setIsRead($isRead)
    {
        $this->is_read = $isRead;
    }

    /**
     * Get is_read
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->is_read;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set user_post_suscription
     *
     * @param Mondel\UserBundle\Entity\UserPostSuscription $userPostSuscription
     */
    public function setUserPostSuscription(\Mondel\UserBundle\Entity\UserPostSuscription $userPostSuscription)
    {
        $this->user_post_suscription = $userPostSuscription;
    }

    /**
     * Get user_post_suscription
     *
     * @return Mondel\UserBundle\Entity\UserPostSuscription 
     */
    public function getUserPostSuscription()
    {
        return $this->user_post_suscription;
    }
}
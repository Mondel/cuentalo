<?php

namespace Mondel\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\UserBundle\Entity\UserPostSuscription
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class UserPostSuscription
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="suscribed_posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Mondel\PostBundle\Entity\Post", inversedBy="suscribed_users")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;

     /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user_post_suscription")
     */
    protected $notifications;

    /*
     * End custom properties
     */
    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set user
     *
     * @param Mondel\UserBundle\Entity\User $user
     */
    public function setUser(\Mondel\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Mondel\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set post
     *
     * @param Mondel\PostBundle\Entity\Post $post
     */
    public function setPost(\Mondel\PostBundle\Entity\Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get post
     *
     * @return Mondel\PostBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Add notifications
     *
     * @param Mondel\UserBundle\Entity\Notification $notifications
     */
    public function addNotification(\Mondel\UserBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;
    }

    /**
     * Get notifications
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}
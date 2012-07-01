<?php

namespace Mondel\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
	Symfony\Component\Validator\Constraints as Assert;


/**
 * Mondel\PostBundle\Entity\Comment
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
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
     * @var string $text
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    /**
     * @var string $ip
     *
     * @Assert\MaxLength(20)
     * @ORM\Column(name="ip", type="string", length=20)
     */
    protected $ip;    

    /**
     * @var boolean $is_active
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $is_active;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Mondel\UserBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->is_active = true;
    }

    public function isActive()
    {
        return $this->getIsActive();
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
     * @param text $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return text 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Comment
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
     * Set is_active
     *
     * @param boolean $isActive
     * @return Comment
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     * @return Comment
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
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
     * Set user
     *
     * @param Mondel\UserBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Mondel\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
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
     * @return Comment
     */
    public function setPost(\Mondel\PostBundle\Entity\Post $post = null)
    {
        $this->post = $post;
        return $this;
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
}
<?php

namespace Mondel\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\PostBundle\Entity\Hashtag
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Hashtag
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
     * @var string $name
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(100)
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

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
     * @ORM\ManyToMany(targetEntity="Post", inversedBy="hashtags")
     * @ORM\JoinTable(name="HashtagPost")
     */
    
    protected $posts;

    public function __construct() {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Hashtag
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Hashtag
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
     * @return Hashtag
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
     * Add posts
     *
     * @param Mondel\PostBundle\Entity\Post $posts
     * @return Hashtag
     */
    public function addPost(\Mondel\PostBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
        return $this;
    }

    /**
     * Remove posts
     *
     * @param <variableType$posts
     */
    public function removePost(\Mondel\PostBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
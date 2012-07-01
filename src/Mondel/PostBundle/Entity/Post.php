<?php

namespace Mondel\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
	Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\PostBundle\Entity\Post
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Post
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
     * @Assert\MaxLength(555)
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
     * @var string $genre
     *
     * @ORM\Column(name="genre", type="string", length=1, nullable=true)
     */
    protected $genre;

    /**
     * @var string $video_url
     *     
     * @ORM\Column(name="video_url", type="string", length=255, nullable=true)
     */
    protected $video_url;
    
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
     * @ORM\ManyToOne(targetEntity="Mondel\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

     /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    protected $comments;

    public function __construct()
    {
    	$this->created_at = new \DateTime();
    	$this->is_active  = true;
        $this->comments   = new \Doctrine\Common\Collections\ArrayCollection();    
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
     * @return Post
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
     * @return Post
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
     * Set genre
     *
     * @param string $genre
     * @return Post
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
        return $this;
    }

    /**
     * Get genre
     *
     * @return string 
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set video_url
     *
     * @param string $videoUrl
     * @return Post
     */
    public function setVideoUrl($videoUrl)
    {
        $this->video_url = $videoUrl;
        return $this;
    }

    /**
     * Get video_url
     *
     * @return string 
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Post
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
     * @return Post
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
     * @return Post
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
     * Set category
     *
     * @param Mondel\PostBundle\Entity\Category $category
     * @return Post
     */
    public function setCategory(\Mondel\PostBundle\Entity\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return Mondel\PostBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add comments
     *
     * @param Mondel\PostBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment(\Mondel\PostBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
        return $this;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}
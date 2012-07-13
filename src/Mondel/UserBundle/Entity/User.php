<?php

namespace Mondel\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\Constraints\Date;

/**
 * Mondel\UserBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mondel\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements AdvancedUserInterface
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
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    protected $name;

    /**
     * @var string $last_name
     *
     * @Assert\MaxLength(50)
     * @Assert\MinLength(3)
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     */
    protected $last_name;

    /**
     * @var string $email
     *
     * @Assert\Email()
     * @Assert\MaxLength(50)
     * @Assert\NotBlank()
     * @ORM\Column(name="email", type="string", length=50)
     */
    protected $email;

    /**
     * @var string $alternative_email
     *
     * @Assert\Email()
     * @Assert\MaxLength(50)     
     * @ORM\Column(name="alternative_email", type="string", length=50, nullable=true)
     */
    protected $alternative_email;

    /**
     * @var string $nick
     *
     * @Assert\MinLength(3)
     * @Assert\MaxLength(50)
     * @Assert\NotBlank()
     * @ORM\Column(name="nick", type="string", length=50)
     */
    protected $nick;

    /**
     * @var string $genre
     *
     * @Assert\Choice({"m", "f", "i"})
     * @ORM\Column(name="genre", type="string", length=1)
     */
    protected $genre;

    /**
     * @var date $birth_date
     *
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    protected $birth_date;

    /**
     * @var date $created_at
     *
     * @ORM\Column(name="created_at", type="date")
     */
    protected $created_at;

    /**
     * @var date $updated_at
     *
     * @ORM\Column(name="updated_at", type="date")
     */
    protected $updated_at;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var string $password
     *
     * @Assert\MaxLength(10)
     * @Assert\MinLength(6)
     * @Assert\NotBlank()
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var boolean $is_active
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $is_active;

    /**
     * @var boolean $is_news_active
     *
     * @ORM\Column(name="is_news_active", type="boolean", nullable=true)
     */
    protected $is_news_active;

    /**
     * @var boolean $is_notifications_active
     *
     * @ORM\Column(name="is_notifications_active", type="boolean", nullable=true)
     */
    protected $is_notifications_active;

    /**
     * @var boolean $is_admin
     *
     * @ORM\Column(name="is_admin", type="boolean")
     */
    protected $is_admin;

    /**
     * @ORM\OneToMany(targetEntity="Mondel\PostBundle\Entity\Post", mappedBy="user")     
     */
    protected $posts;
    
    /**
     * @ORM\OneToMany(targetEntity="Mondel\PostBundle\Entity\Comment", mappedBy="user")
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="UserPostSuscription", mappedBy="user")
     */
    private $suscribed_posts;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="from_user")     
     */
    protected $sent_messages;

    /**
     * @ORM\ManyToMany(targetEntity="Message", mappedBy="to_users")
     */
    protected $received_messages;

    /*
     * Implements AdvancedUserInterface
     */
    public function equals(UserInterface $user)
    {
        return md5($user->getUsername()) === md5($this->getUsername());
    }

    public function eraseCredentials()
    {
        return;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array($this->getIsAdmin() ? 'ROLE_ADMIN' : 'ROLE_USER');
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->getIsActive();
    }
    /*
     * End Implements AdvancedUserInterface
     */

    /**
     * @ORM\PostUpdate
     */
    public function setUpdated()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function __toString()
    {
        return $this->getName() . ' ' . $this->getLastName();
    }

    public function haveUnreadNotifications()
    {
        foreach ($this->getSuscribedPosts() as $suscription) {
            foreach ($suscription->getNotifications() as $notification) {
                if (!$notification->isRead()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getNotifications()
    {
        $notifications = array();

        foreach ($this->getSuscribedPosts() as $suscription) {
            foreach ($suscription->getNotifications() as $notification) {
                array_push($notifications, $notification);
            }
        }
        
        usort($notifications, function($a, $b) {
            if ($a->getCreatedAt() === $b->getCreatedAt()) {
                return 0;
            }
            return ($a->getCreatedAt() > $b->getCreatedAt()) ? -1 : 1;
        });

        return $notifications;
    }

    public function isPostSuscribed($postId)
    {
        foreach ($this->getSuscribedPosts() as $suscription) {
            if ($suscription->getPost()->getId() == $postId) {
                return true;
            }
        }
        return false;
    }

    public function __construct()
    {
        $now = new \DateTime();
        $this->created_at        = $now;
        $this->updated_at        = $now;
        $this->is_active         = false;
        $this->is_admin          = false;
        $this->posts             = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments          = new \Doctrine\Common\Collections\ArrayCollection();
        $this->suscribed_posts   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sent_messages     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->received_messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function isActive()
    {
        return $this->getIsActive();
    }

    /*
     * End custom functions
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
     * @return User
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
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set alternative_email
     *
     * @param string $alternativeEmail
     * @return User
     */
    public function setAlternativeEmail($alternativeEmail)
    {
        $this->alternative_email = $alternativeEmail;
        return $this;
    }

    /**
     * Get alternative_email
     *
     * @return string 
     */
    public function getAlternativeEmail()
    {
        return $this->alternative_email;
    }

    /**
     * Set nick
     *
     * @param string $nick
     * @return User
     */
    public function setNick($nick)
    {
        $this->nick = $nick;
        return $this;
    }

    /**
     * Get nick
     *
     * @return string 
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set genre
     *
     * @param string $genre
     * @return User
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
     * Set birth_date
     *
     * @param date $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;
        return $this;
    }

    /**
     * Get birth_date
     *
     * @return date 
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Set created_at
     *
     * @param date $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get created_at
     *
     * @return date 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param date $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return date 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return User
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
     * Set is_news_active
     *
     * @param boolean $isNewsActive
     * @return User
     */
    public function setIsNewsActive($isNewsActive)
    {
        $this->is_news_active = $isNewsActive;
        return $this;
    }

    /**
     * Get is_news_active
     *
     * @return boolean 
     */
    public function getIsNewsActive()
    {
        return $this->is_news_active;
    }

    /**
     * Set is_notifications_active
     *
     * @param boolean $isNotificationsActive
     * @return User
     */
    public function setIsNotificationsActive($isNotificationsActive)
    {
        $this->is_notifications_active = $isNotificationsActive;
        return $this;
    }

    /**
     * Get is_notifications_active
     *
     * @return boolean 
     */
    public function getIsNotificationsActive()
    {
        return $this->is_notifications_active;
    }

    /**
     * Set is_admin
     *
     * @param boolean $isAdmin
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->is_admin = $isAdmin;
        return $this;
    }

    /**
     * Get is_admin
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Add posts
     *
     * @param Mondel\PostBundle\Entity\Post $posts
     * @return User
     */
    public function addPost(\Mondel\PostBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
        return $this;
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

    /**
     * Add comments
     *
     * @param Mondel\PostBundle\Entity\Comment $comments
     * @return User
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

    /**
     * Get suscribed_posts
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSuscribedPosts()
    {
        return $this->suscribed_posts;
    }

    /**
     * Add sent_messages
     *
     * @param Mondel\UserBundle\Entity\Message $sentMessages
     * @return User
     */
    public function addMessage(\Mondel\UserBundle\Entity\Message $sentMessages)
    {
        $this->sent_messages[] = $sentMessages;
        return $this;
    }

    /**
     * Get sent_messages
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSentMessages()
    {
        return $this->sent_messages;
    }

    /**
     * Get received_messages
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReceivedMessages()
    {
        return $this->received_messages;
    }

    /**
     * Add suscribed_posts
     *
     * @param Mondel\UserBundle\Entity\UserPostSuscription $suscribedPosts
     */
    public function addUserPostSuscription(\Mondel\UserBundle\Entity\UserPostSuscription $suscribedPosts)
    {
        $this->suscribed_posts[] = $suscribedPosts;
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
     * Remove comments
     *
     * @param <variableType$comments
     */
    public function removeComment(\Mondel\PostBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Add suscribed_posts
     *
     * @param Mondel\UserBundle\Entity\UserPostSuscription $suscribedPosts
     * @return User
     */
    public function addSuscribedPost(\Mondel\UserBundle\Entity\UserPostSuscription $suscribedPosts)
    {
        $this->suscribed_posts[] = $suscribedPosts;
        return $this;
    }

    /**
     * Remove suscribed_posts
     *
     * @param <variableType$suscribedPosts
     */
    public function removeSuscribedPost(\Mondel\UserBundle\Entity\UserPostSuscription $suscribedPosts)
    {
        $this->suscribed_posts->removeElement($suscribedPosts);
    }

    /**
     * Add sent_messages
     *
     * @param Mondel\UserBundle\Entity\Message $sentMessages
     * @return User
     */
    public function addSentMessage(\Mondel\UserBundle\Entity\Message $sentMessages)
    {
        $this->sent_messages[] = $sentMessages;
        return $this;
    }

    /**
     * Remove sent_messages
     *
     * @param <variableType$sentMessages
     */
    public function removeSentMessage(\Mondel\UserBundle\Entity\Message $sentMessages)
    {
        $this->sent_messages->removeElement($sentMessages);
    }

    /**
     * Add received_messages
     *
     * @param Mondel\UserBundle\Entity\Message $receivedMessages
     * @return User
     */
    public function addReceivedMessage(\Mondel\UserBundle\Entity\Message $receivedMessages)
    {
        $this->received_messages[] = $receivedMessages;
        return $this;
    }

    /**
     * Remove received_messages
     *
     * @param <variableType$receivedMessages
     */
    public function removeReceivedMessage(\Mondel\UserBundle\Entity\Message $receivedMessages)
    {
        $this->received_messages->removeElement($receivedMessages);
    }
}
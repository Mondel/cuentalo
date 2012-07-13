<?php

namespace Mondel\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
	Symfony\Component\Validator\Constraints as Assert;

/**
 * Mondel\UserBundle\Entity\Message
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Message
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
     * @var string $ip
     *
     * @Assert\MaxLength(20)
     * @ORM\Column(name="ip", type="string", length=20)
     */
    protected $ip;

    /**
     * @var string $title
     *     
     * @Assert\MaxLength(255)
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string $text
     *     
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sent_messages")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $from_user;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="received_messages")
     * @ORM\JoinTable(name="MessageRecipients",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")}
     *      )
     */
    protected $message_recipients;

    public function __construct()
    {
    	$this->created_at = new \DateTime();
        $this->message_recipients = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ip
     *
     * @param string $ip
     * @return Message
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
     * Set title
     *
     * @param string $title
     * @return Message
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param text $text
     * @return Message
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
     * Set created_at
     *
     * @param datetime $createdAt
     * @return Message
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
     * Set from_user
     *
     * @param Mondel\UserBundle\Entity\User $fromUser
     * @return Message
     */
    public function setFromUser(\Mondel\UserBundle\Entity\User $fromUser = null)
    {
        $this->from_user = $fromUser;
        return $this;
    }

    /**
     * Get from_user
     *
     * @return Mondel\UserBundle\Entity\User 
     */
    public function getFromUser()
    {
        return $this->from_user;
    }

    /**
     * Add message_recipients
     *
     * @param Mondel\UserBundle\Entity\User $messageRecipients
     * @return Message
     */
    public function addUser(\Mondel\UserBundle\Entity\User $messageRecipients)
    {
        $this->message_recipients[] = $messageRecipients;
        return $this;
    }

    /**
     * Get message_recipients
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMessageRecipients()
    {
        return $this->message_recipients;
    }

    /**
     * Add message_recipients
     *
     * @param Mondel\UserBundle\Entity\User $messageRecipients
     * @return Message
     */
    public function addMessageRecipient(\Mondel\UserBundle\Entity\User $messageRecipients)
    {
        $this->message_recipients[] = $messageRecipients;
        return $this;
    }

    /**
     * Remove message_recipients
     *
     * @param <variableType$messageRecipients
     */
    public function removeMessageRecipient(\Mondel\UserBundle\Entity\User $messageRecipients)
    {
        $this->message_recipients->removeElement($messageRecipients);
    }
}
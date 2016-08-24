<?php

namespace Blogger\ChatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChatMessage
 *
 * @ORM\Table(name="chat_message")
 * @ORM\Entity(repositoryClass="Blogger\ChatBundle\Repository\ChatMessageRepository")
 */
class ChatMessage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="Chat", inversedBy="messages")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     */
    private $chat;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="chat_messages")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return ChatMessage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return ChatMessage
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }
}


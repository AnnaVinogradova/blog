<?php

namespace Blogger\WallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FriendRequest
 *
 * @ORM\Table(name="friend_request")
 * @ORM\Entity(repositoryClass="Blogger\WallBundle\Repository\FriendRequestRepository")
 */
class FriendRequest
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
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="friend_requests")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="sent_requests")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $sender;

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
     * Set status
     *
     * @param boolean $status
     *
     * @return FriendRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return FriendRequest
     */
    public function setReceiver($user)
    {
        $this->receiver = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return FriendRequest
     */
    public function setSender($user)
    {
        $this->sender = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }
   
}


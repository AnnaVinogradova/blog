<?php

namespace Blogger\TodolistBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Request
 *
 * @ORM\Table(name="request")
 * @ORM\Entity(repositoryClass="Blogger\TodolistBundle\Repository\RequestRepository")
 */
class Request
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
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="requests")
     * @ORM\JoinColumn(name="todolist_id", referencedColumnName="id")
     */
    private $todolist;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="requests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Assert\NotNull()
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
     * Set status
     *
     * @param boolean $status
     *
     * @return Request
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
     * Set todolist
     *
     * @param string $todolist
     *
     * @return TodoList
     */
    public function setTodolist($todolist)
    {
        $this->todolist = $todolist;

        return $this;
    }

    /**
     * Get todolist
     *
     * @return string
     */
    public function getTodolist()
    {
        return $this->todolist;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return TodoList
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

}


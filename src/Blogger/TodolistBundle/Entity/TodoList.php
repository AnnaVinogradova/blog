<?php

namespace Blogger\TodolistBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Todolist
 *
 * @ORM\Table(name="todolist")
 * @ORM\Entity(repositoryClass="Blogger\TodolistBundle\Repository\TodolistRepository")
 */
class TodoList
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="todolists")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="todolist", cascade={"persist", "remove"})
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="todolist", cascade={"persist", "remove"})
     */
    private $requests;

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
     * Set name
     *
     * @param string $name
     *
     * @return Todolist
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

    /**
     * Add task
     *
     * @param Blogger\TodolistBundle\Entity\Task $task
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
        $task->setTodolist($this);
        return $this;
    }

    /**
     * Get tasks
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }
 
    /**
     * Add request
     *
     * @param Blogger\TodolistBundle\Entity\Request $request
     */
    public function addRequest(Request $request)
    {
        $this->requests[] = $request;
        $request->setTodolist($this);
        return $this;
    }

    /**
     * Get requests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
    }

    public function __construct()
    {
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


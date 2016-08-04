<?php

namespace Blogger\BlogBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
    * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\TodolistBundle\Entity\TodoList", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $todolists;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\TodolistBundle\Entity\Request", mappedBy="user", cascade={"persist", "remove"})
     */
    private $requests;

    public function addPost(Post $post)
    {
        $this->posts[] = $post;
        $post->setAuthor($this);
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
 
    public function addRequest(\Blogger\TodolistBundle\Entity\Request $request)
    {
        $this->requests[] = $request;
        $request->setUser($this);
        return $this;
    }
     
    /**
     * Get messages
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function addTodolist(\Blogger\TodolistBundle\Entity\TodoList $todolist)
    {
        $this->todolists[] = $todolist;
        $todolist->setUser($this);
        return $this;
    }
     
    /**
     * Get todolists
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTodolists()
    {
        return $this->todolists;
    }

    public function __construct()
    {
    	parent::__construct();
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->todolists = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

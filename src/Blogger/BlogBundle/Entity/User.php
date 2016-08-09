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
     * @ORM\OneToOne(targetEntity="Blogger\MapBundle\Entity\Map", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $map;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\TodolistBundle\Entity\Request", mappedBy="user", cascade={"persist", "remove"})
     */
    private $requests;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\MapBundle\Entity\MapResolver", mappedBy="user", cascade={"persist", "remove"})
     */
    private $map_resolvers;

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
     * Get map_resolver
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMapResolvers()
    {
        return $this->map_resolvers;
    }
 
    public function addMapResolver(\Blogger\MapBundle\Entity\MapResolver $request)
    {
        $this->map_resolvers[] = $request;
        $request->setUser($this);
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

    public function getMap()
    {
        return $this->map;
    }

    public function addMap($map){
        $this->map = $map;
        $map->setUser($this);
        return $this;
    }
}

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
     * @ORM\OneToOne(targetEntity="Blogger\WallBundle\Entity\Wall", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $wall;

    /**
     * @ORM\OneTomany(targetEntity="Blogger\WallBundle\Entity\WallPost", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $wall_posts;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\TodolistBundle\Entity\Request", mappedBy="user", cascade={"persist", "remove"})
     */
    private $requests;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\WallBundle\Entity\FriendRequest", mappedBy="receiver", cascade={"persist", "remove"})
     */
    private $friend_requests;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\WallBundle\Entity\FriendRequest", mappedBy="sender", cascade={"persist", "remove"})
     */
    private $sent_requests;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\MapBundle\Entity\MapResolver", mappedBy="user", cascade={"persist", "remove"})
     */
    private $map_resolvers;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\GameBundle\Entity\Game", mappedBy="player1", cascade={"persist", "remove"})
     */
    private $player1_games;

    /**
     * @ORM\OneToMany(targetEntity="Blogger\GameBundle\Entity\Game", mappedBy="player2", cascade={"persist", "remove"})
     */
    private $player2_games;

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

    public function getWall()
    {
        return $this->wall;
    }

    public function addWall($wall){
        $this->wall = $wall;
        $wall->setUser($this);
        return $this;
    }

    /**
     * Get requests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFriendRequests()
    {
        return $this->friend_requests;
    }
 
    public function addFriendRequest(\Blogger\WallBundle\Entity\FriendRequest $request)
    {
        $this->friend_requests[] = $request;
        $request->setReceiver($this);
        return $this;
    }

    /**
     * Get requests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMyRequests()
    {
        return $this->sent_requests;
    }
 
    public function addMyRequest(\Blogger\WallBundle\Entity\FriendRequest $request)
    {
        $this->sent_requests[] = $request;
        $request->setSender($this);
        return $this;
    }

    public function isFriend($securityContext, $context){

        if(!$securityContext->isGranted('ROLE_ADMIN')){
            $user = $securityContext->getToken()->getUser();
            $em = $context->getDoctrine()->getManager();
            return $this->checkAccess($user, $em); 
        }
        return true;
    }

    private function checkAccess($user, $em)
    {
        if($em->getRepository('BloggerWallBundle:FriendRequest')->findOneBy(
                    array('sender' => $user,
                          'receiver' => $this,
                          'status' => true)
                )
        ) {
            return true;
        }

        return $em->getRepository('BloggerWallBundle:FriendRequest')->findOneBy(
                    array('sender' => $this,
                          'receiver' => $user,
                          'status' => true)
                );
    }

}

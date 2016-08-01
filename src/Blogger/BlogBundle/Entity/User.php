<?php

namespace Blogger\BlogBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
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
     * @ORM\OneToMany(targetEntity="Post", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $posts;

    public function addPost(Post $post)
    {
        $this->posts[] = $post;
        $post->setAuthor($this);
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
 
     
    public function __construct()
    {
    	parent::__construct();
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

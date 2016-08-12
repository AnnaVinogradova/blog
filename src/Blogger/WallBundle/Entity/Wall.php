<?php

namespace Blogger\WallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wall
 *
 * @ORM\Table(name="wall")
 * @ORM\Entity(repositoryClass="Blogger\WallBundle\Repository\WallRepository")
 */
class Wall
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
     * @ORM\OneToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="wall")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="WallPost", mappedBy="wall", cascade={"persist", "remove"})
     */
    private $posts;


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
     * Add post
     *
     * @param Blogger\WallBundle\Entity\WallPost $post
     */
    public function addPost(WallPost $post)
    {
        $this->posts[] = $post;
        $post->setWall($this);
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

        public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


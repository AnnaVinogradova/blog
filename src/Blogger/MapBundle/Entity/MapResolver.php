<?php

namespace Blogger\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MapResolver
 *
 * @ORM\Table(name="map_resolver")
 * @ORM\Entity(repositoryClass="Blogger\MapBundle\Repository\MapResolverRepository")
 */
class MapResolver
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
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="resolvers")
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     */
    private $map;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="map_resolvers")
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
     * Set map
     *
     * @param string $map
     *
     * @return Map
     */
    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map
     *
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
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
     * Set status
     *
     * @param boolean $status
     *
     * @return MapResolver
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
}


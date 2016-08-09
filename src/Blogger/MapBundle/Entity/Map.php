<?php

namespace Blogger\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Map
 *
 * @ORM\Table(name="map")
 * @ORM\Entity(repositoryClass="Blogger\MapBundle\Repository\MapRepository")
 */
class Map
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
     * @ORM\OneToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="map")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Marker", mappedBy="map", cascade={"persist", "remove"})
     */
    private $markers;

    /**
     * @ORM\OneToMany(targetEntity="MapResolver", mappedBy="map", cascade={"persist", "remove"})
     */
    private $map_resolvers;

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
     * Add task
     *
     * @param Blogger\MapBundle\Entity\Marker $marker
     */
    public function addMarker(Marker $marker)
    {
        $this->markers[] = $marker;
        $marker->setMap($this);
        return $this;
    }

    /**
     * Get markers
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    public function __construct()
    {
        $this->markers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->map_resolvers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get todolist
     *
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }

     public function addMapResolver(MapResolver $request)
    {
        $this->map_resolvers[] = $request;
        $request->setMap($this);
        return $this;
    }

    /**
     * Get requests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMapResolvers()
    {
        return $this->map_resolvers;
    }

}

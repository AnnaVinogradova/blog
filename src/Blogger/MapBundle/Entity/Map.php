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
    public function addMarker(Task $marker)
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
    }

}

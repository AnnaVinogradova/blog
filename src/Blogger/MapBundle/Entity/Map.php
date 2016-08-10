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
    const OWNER_ROLE = 1;
    const RESOLVER_ROLE = 0;
    const RQUEST_TO_RESOLVER = 2;

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

    public function isAccessable($securityContext, $context, $role){
        if(!$securityContext->isGranted('ROLE_ADMIN')){
            $user = $securityContext->getToken()->getUser();
            $em = $context->getDoctrine()->getManager();
            if($role == self::RESOLVER_ROLE){
                return $this->checkAccess($user, $em); 
            } elseif($role == self::OWNER_ROLE) {
                return $this->checkIsCreator($user, $em);
            } else {
                return $this->checkResolver($user, $em); 
            }
        }
        return true;
    }

    private function checkAccess($user, $em)
    {
        if($this->checkIsCreator($user, $em)){
            return true;
        }
        return  $em->getRepository('BloggerMapBundle:MapResolver')->findBy( 
                array('map' => $this,
                'user' => $user,
                'status' => true)
                );
    }

    private function checkResolver($user, $em)
    {
        if($this->checkIsCreator($user, $em)){
            return true;
        }
        return  $em->getRepository('BloggerMapBundle:MapResolver')->findBy( 
                array('map' => $this,
                'user' => $user)
                );
    }

    private function checkIsCreator($user, $em)
    {
        return  $em->getRepository('BloggerMapBundle:Map')->findBy( 
                array('id' => $this->getId(),
                'user' => $user)
                );
    }
}

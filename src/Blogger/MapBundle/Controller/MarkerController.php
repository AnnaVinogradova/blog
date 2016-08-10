<?php

namespace Blogger\MapBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\MapBundle\Entity\Marker;
use Blogger\MapBundle\Entity\Map;
use Blogger\MapBundle\Form\MarkerType;

/**
 * Marker controller.
 *
 * @Route("/marker")
 */
class MarkerController extends Controller
{
    /**
     * Lists all Marker entities with creation form.
     *
     * @Route("/{id}", name="marker_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $map = $em->getRepository('BloggerMapBundle:Map')->findOneBy(array('id' => $id));

        $securityContext = $this->container->get('security.context');
        if(!$map->isAccessable($securityContext, $this, Map::RESOLVER_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

        $markers = $map->getMarkers();
        $marker = new Marker();
        $form = $this->createForm('Blogger\MapBundle\Form\MarkerType', $marker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $marker->setMap($map);
            $em->persist($marker);
            $em->flush();

            return $this->render('marker/index.html.twig', array(
                'id' => $id,
                'markers' => $markers,
                'form' => $form->createView(),
            ));
        }

        return $this->render('marker/index.html.twig', array(
            'id' => $id,
            'markers' => $markers,
            'form' => $form->createView(),
        ));
    }

    /**
     * Menu page for Map.
     *
     * @Route("/", name="map_index")
     * @Method({"GET"})
     */
    public function mapAction()
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $waiting = array();
            $accessable = array();
            $map = null;

            if(! $map = $user->getMap()){
                $em = $this->getDoctrine()->getManager();
                $map = new Map();
                $map->setUser($user);
                $em->persist($map);
                $em->flush();
            } else {
                $mapResolvers = $user->getMapResolvers();
                foreach ($mapResolvers as $resolver) {
                    if(! $resolver->getStatus()){
                        $waiting[] = $resolver;
                    } else {
                        $accessable[] = $resolver->getMap();
                    }
                }
            }           

            return $this->render('marker/map.html.twig', array(
                'map' => $map->getId(),
                'requests' => $waiting,
                'accessable' => $accessable,
            ));
        } else {
            return $this->render('post/access_denied.html.twig');
        }
    }

    /**
     * Creates a form to delete a Marker entity.
     *
     * @param Marker $marker The Marker entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Marker $marker)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marker_delete', array('id' => $marker->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

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
     * Lists all Marker entities.
     *
     * @Route("/{id}", name="marker_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $map = $em->getRepository('BloggerMapBundle:Map')->findOneBy(array('id' => $id));
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
     * Lists all Marker entities.
     *
     * @Route("/", name="map_index")
     * @Method({"GET"})
     */
    public function mapAction()
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $map = null;
        if(! $map = $user->getMap()){
            $em = $this->getDoctrine()->getManager();
            $map = new Map();
            $map->setUser($user);
            $em->persist($map);
            $em->flush();
        }

        $mapResolvers = $user->getMapResolvers();
        $waiting = array();
        $accessable = array();
        foreach ($mapResolvers as $resolver) {
            if(! $resolver->getStatus()){
                $waiting[] = $resolver;
            } else {
                $accessable[] = $resolver->getMap();
            }
        }

        return $this->render('marker/map.html.twig', array(
            'map' => $map->getId(),
            'requests' => $waiting,
            'accessable' => $accessable,
        ));
    }

    /**
     * Creates a new Marker entity.
     *
     * @Route("/new/{id}", name="marker_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $map = $em->getRepository('BloggerMapBundle:Map')->findOneBy(array('id' => $id));

        $marker = new Marker();
        $form = $this->createForm('Blogger\MapBundle\Form\MarkerType', $marker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $marker->setMap($map);
            $em->persist($marker);
            $em->flush();

            return $this->redirectToRoute('marker_show', array('id' => $marker->getId()));
        }

        return $this->render('marker/new.html.twig', array(
            'marker' => $marker,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Marker entity.
     *
     * @Route("/{id}", name="marker_show")
     * @Method("GET")
     */
    public function showAction(Marker $marker)
    {
        $deleteForm = $this->createDeleteForm($marker);

        return $this->render('marker/show.html.twig', array(
            'marker' => $marker,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Marker entity.
     *
     * @Route("/{id}/edit", name="marker_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Marker $marker)
    {
        $deleteForm = $this->createDeleteForm($marker);
        $editForm = $this->createForm('Blogger\MapBundle\Form\MarkerType', $marker);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($marker);
            $em->flush();

            return $this->redirectToRoute('marker_edit', array('id' => $marker->getId()));
        }

        return $this->render('marker/edit.html.twig', array(
            'marker' => $marker,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Marker entity.
     *
     * @Route("/{id}", name="marker_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Marker $marker)
    {
        $form = $this->createDeleteForm($marker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marker);
            $em->flush();
        }

        return $this->redirectToRoute('marker_index');
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

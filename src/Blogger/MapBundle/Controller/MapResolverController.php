<?php

namespace Blogger\MapBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\MapBundle\Entity\MapResolver;
use Blogger\MapBundle\Form\MapResolverType;

/**
 * MapResolver controller.
 *
 * @Route("/mapresolver")
 */
class MapResolverController extends Controller
{
    /**
     * Lists all MapResolver entities.
     *
     * @Route("/map/{id}", name="mapresolver_index")
     * @Method("GET")
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $map = $em->getRepository('BloggerMapBundle:Map')->findOneBy(array('id' => $id));

        $mapResolvers = $map->getMapResolvers();

        return $this->render('mapresolver/index.html.twig', array(
            'id' => $id,
            'mapResolvers' => $mapResolvers,
        ));
    }

    /**
     * Creates a new MapResolver entity.
     *
     * @Route("/new/{id}", name="mapresolver_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $map = $em->getRepository('BloggerMapBundle:Map')->findOneBy(array('id' => $id));

        $mapResolver = new MapResolver();
        $form = $this->createForm('Blogger\MapBundle\Form\MapResolverType', $mapResolver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mapResolver->setMap($map);
            $em->persist($mapResolver);
            $em->flush();

            return $this->redirectToRoute('mapresolver_index', array('id' => $id));
        }

        return $this->render('mapresolver/new.html.twig', array(
            'id' => $id,
            'mapResolver' => $mapResolver,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MapResolver entity.
     *
     * @Route("/{id}", name="mapresolver_show")
     * @Method("GET")
     */
    public function showAction(MapResolver $mapResolver)
    {
        $deleteForm = $this->createDeleteForm($mapResolver);

        return $this->render('mapresolver/show.html.twig', array(
            'mapResolver' => $mapResolver,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MapResolver entity.
     *
     * @Route("/{id}/edit", name="mapresolver_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MapResolver $mapResolver)
    {
        $deleteForm = $this->createDeleteForm($mapResolver);
        $editForm = $this->createForm('Blogger\MapBundle\Form\MapResolverType', $mapResolver);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mapResolver);
            $em->flush();

            return $this->redirectToRoute('mapresolver_edit', array('id' => $mapResolver->getId()));
        }

        return $this->render('mapresolver/edit.html.twig', array(
            'mapResolver' => $mapResolver,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a MapResolver entity.
     *
     * @Route("/{id}", name="mapresolver_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MapResolver $mapResolver)
    {
        $id = $mapResolver->getMap()->getId();
        $form = $this->createDeleteForm($mapResolver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mapResolver);
            $em->flush();
        }

        return $this->redirectToRoute('mapresolver_index', array('id' => $id));
    }

    /**
     * Creates a form to delete a MapResolver entity.
     *
     * @param MapResolver $mapResolver The MapResolver entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MapResolver $mapResolver)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mapresolver_delete', array('id' => $mapResolver->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

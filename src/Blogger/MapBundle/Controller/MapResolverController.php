<?php

namespace Blogger\MapBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\MapBundle\Entity\MapResolver;
use Blogger\MapBundle\Entity\Map;
use Blogger\MapBundle\Form\MapResolverType;
use Symfony\Component\Form\FormError;

/**
 * MapResolver controller.
 *
 * @Route("/mapresolver")
 */
class MapResolverController extends Controller
{
    /**
     * Lists all MapResolver entities for map.
     *
     * @Route("/map/{id}", name="mapresolver_index")
     * @Method("GET")
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $map = $em->getRepository('BloggerMapBundle:Map')->findOneBy(array('id' => $id));

        $securityContext = $this->container->get('security.context');
        if(!$map->isAccessable($securityContext, $this, Map::OWNER_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

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

        $securityContext = $this->container->get('security.context');
        if(!$map->isAccessable($securityContext, $this, Map::OWNER_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

        $mapResolver = new MapResolver();
        $form = $this->createForm('Blogger\MapBundle\Form\MapResolverType', $mapResolver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $securityContext->getToken()->getUser();
            if($mapResolver->getUser() == $user){
                $form->addError(new FormError("It's your own map"));
            } elseif($this->getDoctrine()->getRepository('BloggerMapBundle:MapResolver')
                    ->findBy(array('map' => $map, 'user' => $mapResolver->getUser()))){
                        $form->addError(new FormError("This request already exists. Please, waiting for user's resolve"));

            } else {
                $mapResolver->setMap($map);
                $mapResolver->setStatus(false);
                $em->persist($mapResolver);
                $em->flush();
                        
                return $this->redirectToRoute('mapresolver_index', array('id' => $id));
            }
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
        $map = $mapResolver->getMap();
        $securityContext = $this->container->get('security.context');
        if(!$map->isAccessable($securityContext, $this, Map::OWNER_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

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
        $editForm = $this->createForm('Blogger\MapBundle\Form\AcceptMapResolverType', $mapResolver);
        $editForm->handleRequest($request);

        $map = $mapResolver->getMap();
        $securityContext = $this->container->get('security.context');
        if(!$map->isAccessable($securityContext, $this, Map::COULD_BE_RESOLVER)){
            return $this->render('post/access_denied.html.twig');
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $name = $mapResolver->getUser();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('BloggerBlogBundle:User')->findOneBy(array('username' => $name));
            
            $mapResolver->setStatus(true);
            $mapResolver->setUser($user);
            $em->persist($mapResolver);
            $em->flush();

            return $this->redirectToRoute('map_index');
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
        $map = $mapResolver->getMap();
        $form = $this->createDeleteForm($mapResolver);
        $form->handleRequest($request);

        $securityContext = $this->container->get('security.context');
        if(!$map->isAccessable($securityContext, $this, Map::RQUEST_TO_RESOLVER)){
            return $this->render('post/access_denied.html.twig');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mapResolver);
            $em->flush();
        }

        return $this->redirectToRoute('map_index');
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

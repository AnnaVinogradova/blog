<?php

namespace Blogger\WallBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\WallBundle\Entity\FriendRequest;
use Blogger\WallBundle\Form\FriendRequestType;

/**
 * FriendRequest controller.
 *
 * @Route("/friendlist")
 */
class FriendRequestController extends Controller
{
    /**
     * Lists all FriendRequest entities for user.
     *
     * @Route("/", name="friendlist_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $acceptable = array();
        $waiting = array();
        $friendRequests = $user->getFriendRequests();

        foreach ($friendRequests as $request) {
            if($request->getStatus()){
                $deleteForm = $this->createDeleteForm($request);
                $request->form = $deleteForm->createView();
                $acceptable[] = $request;
            } else {
                $waiting[] = $request;
            }
        }

        return $this->render('friendrequest/index.html.twig', array(
            'requests' => $waiting,
            'friends' => $acceptable,
        ));
    }

    /**
     * Creates a new FriendRequest entity.
     *
     * @Route("/{id}/new", name="friendrequest_new")
     * @Method({"GET"})
     */
    public function newAction($id)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $wall = $em->getRepository('BloggerWallBundle:Wall')->findOneBy(
                    array('id' => $id)
                );
        $owner = $wall->getUser();

        $friendRequest = new FriendRequest();
        $friendRequest->setSender($user);
        $friendRequest->setReceiver($owner);
        $friendRequest->setStatus(false);
        $em->persist($friendRequest);
        $em->flush();

        return $this->redirectToRoute('wall_index', array('id' => $id));

    }

    /**
     * Finds and displays a FriendRequest entity.
     *
     * @Route("/{id}", name="friendrequest_show")
     * @Method("GET")
     */
    public function showAction(FriendRequest $friendRequest)
    {
        $deleteForm = $this->createDeleteForm($friendRequest);

        return $this->render('friendrequest/show.html.twig', array(
            'friendRequest' => $friendRequest,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FriendRequest entity.
     *
     * @Route("/{id}/edit", name="friendrequest_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, FriendRequest $friendRequest)
    {
        $deleteForm = $this->createDeleteForm($friendRequest);
        $editForm = $this->createForm('Blogger\WallBundle\Form\FriendRequestType', $friendRequest);
        $editForm->handleRequest($request);
        $id = $friendRequest->getId();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $request = $em->getRepository('BloggerWallBundle:FriendRequest')->findOneBy(
                    array('id' => $id)
                );
            $request->setStatus(true);
            $em->persist($request);
            $em->flush();

            return $this->redirectToRoute('friendlist_index');
        }

        return $this->render('friendrequest/edit.html.twig', array(
            'friendRequest' => $friendRequest,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a FriendRequest entity.
     *
     * @Route("/{id}", name="friendrequest_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, FriendRequest $friendRequest)
    {
        $form = $this->createDeleteForm($friendRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($friendRequest);
            $em->flush();
        }

        return $this->redirectToRoute('friendlist_index');
    }

    /**
     * Creates a form to delete a FriendRequest entity.
     *
     * @param FriendRequest $friendRequest The FriendRequest entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(FriendRequest $friendRequest)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('friendrequest_delete', array('id' => $friendRequest->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

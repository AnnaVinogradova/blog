<?php

namespace Blogger\WallBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\WallBundle\Entity\FriendRequest;
use Blogger\BlogBundle\Entity\User;
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
        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $friendRequests = $user->getFriendRequests();
            $myRequests = $user->getMyRequests();
            $acceptable = array();
            $yours = array();
            $waiting = array();            

            foreach ($friendRequests as $request) {
                if($request->getStatus()){
                    $deleteForm = $this->createDeleteForm($request);
                    $request->form = $deleteForm->createView();
                    $acceptable[] = $request;
                } else {
                    $waiting[] = $request;
                }
            }

            foreach ($myRequests as $request) {
                if($request->getStatus()){
                    $deleteForm = $this->createDeleteForm($request);
                    $request->form = $deleteForm->createView();
                    $yours[] = $request;
                }
            }

            return $this->render('friendrequest/index.html.twig', array(
                'requests' => $waiting,
                'friends' => $acceptable,
                'yours' => $yours
            ));
        } else {            
            throw new AccessDeniedException();
        }
    }

    /**
     * Lists all User entities or Single User from request.
     *
     * @Route("/list", name="user_list")
     * @Method({"GET", "POST"})
     */
    public function listAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('Blogger\WallBundle\Form\UserFindType', $user);
        $form->handleRequest($request);

        $finder = $this->container->get('fos_elastica.finder.app.user');
            
        $results = $finder->find($user->getUsername());
        if(!$results){
            return $this->render('friendrequest/users.html.twig', array(
                'form' => $form->createView(),
                ));
        }
        return $this->render('friendrequest/users.html.twig', array(
            "list" => $results,
            'form' => $form->createView(),
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
        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            $wall = $em->getRepository('BloggerWallBundle:Wall')->findOneBy(
                        array('id' => $id)
                    );
            $owner = $wall->getUser();

            if($this->isRequestExists($owner, $user)){
                return $this->redirectToRoute('wall_index', array('id' => $id));
            }

            $friendRequest = new FriendRequest();
            $friendRequest->setSender($user);
            $friendRequest->setReceiver($owner);
            $friendRequest->setStatus(false);
            $em->persist($friendRequest);
            $em->flush();

            return $this->redirectToRoute('wall_index', array('id' => $id));
        } else {            
            throw new AccessDeniedException();
        }
    }

    /**
     * Displays a form to edit an existing FriendRequest entity only for receiver.
     *
     * @Route("/{id}/edit", name="friendrequest_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, FriendRequest $friendRequest)
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $deleteForm = $this->createDeleteForm($friendRequest);
            $editForm = $this->createForm('Blogger\WallBundle\Form\FriendRequestType', $friendRequest);
            $editForm->handleRequest($request);

            $id = $friendRequest->getId();
            $em = $this->getDoctrine()->getManager(); 
            $request = $em->getRepository('BloggerWallBundle:FriendRequest')->findOneBy(
                        array('id' => $id)
                    );
            $user = $securityContext->getToken()->getUser();
            if($user == $request->getReceiver()){
                if ($editForm->isSubmitted() && $editForm->isValid()) {     
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
        }

        throw new AccessDeniedException();        
    }

    /**
     * Deletes a FriendRequest entity.
     *
     * @Route("/{id}", name="friendrequest_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, FriendRequest $friendRequest)
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $form = $this->createDeleteForm($friendRequest);
            $form->handleRequest($request);
            
            $user = $securityContext->getToken()->getUser();
            if(($user == $friendRequest->getReceiver()) || ($user == $friendRequest->getSender())){
                if ($form->isSubmitted() && $form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($friendRequest);
                    $em->flush();
                }

                return $this->redirectToRoute('friendlist_index');
            }
        } 
        throw new AccessDeniedException();      
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

    private function isRequestExists($owner, $user)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('BloggerWallBundle:FriendRequest');

        if($repo->findOneBy(
                        array('sender' => $user,
                              'receiver' => $owner
                            ))){
                            return true;
                        }

        return $repo->findOneBy(
                        array('sender' => $owner,
                              'receiver' => $user
                            ));
    }
}

<?php

namespace Blogger\TodolistBundle\Controller;

use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Blogger\TodolistBundle\Entity\Request;
use Blogger\TodolistBundle\Entity\TodoList;
use Blogger\TodolistBundle\Form\RequestType;
use Symfony\Component\Form\FormError;

/**
 * Request controller.
 *
 * @Route("/request")
 */
class RequestController extends Controller
{
    /**
     * List of Requests for todolist.
     *
     * @Route("/list/{id}", name="request_index")
     * @Method("GET")
     */
    public function indexAction($id)
    {    
        $todoList = $this
            ->getDoctrine()
            ->getRepository('BloggerTodolistBundle:TodoList')
            ->find($id);

        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::CREATOR_ROLE)){
            throw new AccessDeniedException();
        }

        $requests = $todoList->getRequests();
        return $this->render('request/index.html.twig', array(
                'list' => $id,
                'requests' => $requests,
            ));

    }

    /**
     * All Requests to admin.
     *
     * @Route("/", name="request_control")
     * @Method("GET")
     */
    public function adminAction()
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getManager();
            $requests = $em->getRepository('BloggerTodolistBundle:Request')->findAll(); 

            return $this->render('request/admin.html.twig', array(
                'requests' => $requests,
            ));
        }
        throw new AccessDeniedException();
    }

    /**
     * Creates a new Request entity.
     *
     * @Route("/{id}/new", name="request_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(HTTPRequest $http_request, $id)
    {
        $todoList = $this
            ->getDoctrine()
            ->getRepository('BloggerTodolistBundle:TodoList')
            ->find($id);

        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::CREATOR_ROLE)){
                throw new AccessDeniedException();
        }

        $request = new Request();
        $form = $this->createForm('Blogger\TodolistBundle\Form\RequestType', $request);
        $form->handleRequest($http_request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($this->getDoctrine()->getRepository('BloggerTodolistBundle:Request')
                     ->findBy(array('todolist' => $todoList, 'user' => $request->getUser()))){
                            $form->addError(new FormError("This request already exists. Please, waiting for admin's checking"));
                            return $this->render('request/new.html.twig', array(
                                'request' => $request,
                                'list' => $id,
                                'form' => $form->createView(),
                            ));
            }

            $em = $this->getDoctrine()->getManager();    
            $request->setStatus(null);
            $todoList->addRequest($request);
            $em->flush();

            return $this->redirectToRoute('request_show', array('id' => $request->getId()));
        }

        return $this->render('request/new.html.twig', array(
            'request' => $request,
            'list' => $id,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Request entity.
     *
     * @Route("/{id}", name="request_show")
     * @Method("GET")
     */
    public function showAction(Request $request)
    {
        $todoList = $request->getTodolist();

        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::CREATOR_ROLE)){
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($request);

        return $this->render('request/show.html.twig', array(
            'request' => $request,
            'list' => $todoList->getId(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Request entity.
     *
     * @Route("/{id}/edit", name="request_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(HTTPRequest $http_request, Request $request)
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_ADMIN')){
            $deleteForm = $this->createDeleteForm($request);
            $editForm = $this->createForm('Blogger\TodolistBundle\Form\RequestType', $request);
            $editForm->handleRequest($http_request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $request->setStatus(true);
                $em->persist($request);
                $em->flush();

                return $this->redirectToRoute('request_control');
            }

            return $this->render('request/edit.html.twig', array(
                'request' => $request,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        }
        throw new AccessDeniedException();
    }

    /**
     * Deletes a Request entity.
     *
     * @Route("/{id}", name="request_delete")
     * @Method("DELETE")
     */
    public function deleteAction(HTTPRequest $http_request, Request $request)
    {
        $form = $this->createDeleteForm($request);
        $form->handleRequest($http_request);
        $list = $request->getTodolist();

        $securityContext = $this->container->get('security.context');
        if(!$list->isAccessable($securityContext, $this, TodoList::CREATOR_ROLE)){
            throw new AccessDeniedException(); 
        }
      
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($request);
            $em->flush();
        }

        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('request_control');
        }
        return $this->redirectToRoute('request_index', array('id' => $list->getId()));
    }

    /**
     * Creates a form to delete a Request entity.
     *
     * @param Request $request The Request entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Request $request)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('request_delete', array('id' => $request->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}

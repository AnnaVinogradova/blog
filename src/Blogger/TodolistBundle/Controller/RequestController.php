<?php

namespace Blogger\TodolistBundle\Controller;

use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\TodolistBundle\Entity\Request;
use Blogger\TodolistBundle\Form\RequestType;

/**
 * Request controller.
 *
 * @Route("/request")
 */
class RequestController extends Controller
{
    /**
     * Lists all Request entities.
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

        $requests = $todoList->getRequests();

        return $this->render('request/index.html.twig', array(
            'list' => $id,
            'requests' => $requests,
        ));
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

        $request = new Request();
        $form = $this->createForm('Blogger\TodolistBundle\Form\RequestType', $request);
        $form->handleRequest($http_request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $deleteForm = $this->createDeleteForm($request);

        return $this->render('request/show.html.twig', array(
            'request' => $request,
            'list' => $request->getTodolist()->getId(),
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
        $deleteForm = $this->createDeleteForm($request);
        $editForm = $this->createForm('Blogger\TodolistBundle\Form\RequestType', $request);
        $editForm->handleRequest($http_request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($request);
            $em->flush();

            return $this->redirectToRoute('request_edit', array('id' => $request->getId()));
        }

        return $this->render('request/edit.html.twig', array(
            'request' => $request,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
        $id = $request->getTodolist()->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($request);
            $em->flush();
        }

        return $this->redirectToRoute('request_index', array('id' => $id));
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

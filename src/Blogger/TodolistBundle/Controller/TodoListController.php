<?php

namespace Blogger\TodolistBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\TodolistBundle\Entity\TodoList;
use Blogger\TodolistBundle\Form\TodoListType;

/**
 * TodoList controller.
 *
 * @Route("/todolist")
 */
class TodoListController extends Controller
{
    /**
     * All accessable TodoList entities.
     *
     * @Route("/", name="todolist_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');

        if(!$securityContext->isGranted('ROLE_ADMIN')){
            if($securityContext->isGranted('ROLE_USER')){
                $user = $securityContext->getToken()->getUser();
                $todoLists = $user->getTodolists();
                $requests = $user->getRequests();
                $accessable = array();
                foreach ($requests as $request) {
                    if($request->getStatus()){
                        $accessable[] = $request->getTodolist();
                    }
                }

                return $this->render('todolist/index.html.twig', array(
                    'todoLists' => $todoLists,
                    'accessable' => $accessable
                ));
            } else {
                return $this->render('post/access_denied.html.twig');
            }
        }
        
        $em = $this->getDoctrine()->getManager();
        $todoLists = $em->getRepository('BloggerTodolistBundle:TodoList')->findAll();
        return $this->render('todolist/index.html.twig', array(
                'todoLists' => $todoLists
            )); 
        
    }

    /**
     * Creates a new TodoList entity.
     *
     * @Route("/new", name="todolist_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $todoList = new TodoList();
            $form = $this->createForm('Blogger\TodolistBundle\Form\TodoListType', $todoList);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user = $securityContext->getToken()->getUser();
                $user->addTodolist($todoList);
                $em->flush();

                return $this->redirectToRoute('todolist_show', array('id' => $todoList->getId()));
            }

            return $this->render('todolist/new.html.twig', array(
                'todoList' => $todoList,
                'form' => $form->createView(),
            ));
        } else {
                return $this->render('post/access_denied.html.twig');
            }
    }

    /**
     * Finds and displays a TodoList entity.
     *
     * @Route("/{id}", name="todolist_show")
     * @Method("GET")
     */
    public function showAction(TodoList $todoList)
    {
        $securityContext = $this->container->get('security.context');
        if(!TodoList::isAccessable($securityContext, $this, $todoList, TodoList::USER_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

        $deleteForm = $this->createDeleteForm($todoList);

        return $this->render('todolist/show.html.twig', array(
            'todoList' => $todoList,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TodoList entity.
     *
     * @Route("/{id}/edit", name="todolist_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TodoList $todoList)
    {
        $deleteForm = $this->createDeleteForm($todoList);
        $editForm = $this->createForm('Blogger\TodolistBundle\Form\TodoListType', $todoList);
        $editForm->handleRequest($request);

        $securityContext = $this->container->get('security.context');
        if(!TodoList::isAccessable($securityContext, $this, $todoList, TodoList::CREATOR_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todoList);
            $em->flush();

            return $this->redirectToRoute('todolist_edit', array('id' => $todoList->getId()));
        }

        return $this->render('todolist/edit.html.twig', array(
            'todoList' => $todoList,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TodoList entity.
     *
     * @Route("/{id}", name="todolist_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TodoList $todoList)
    {
        $form = $this->createDeleteForm($todoList);
        $form->handleRequest($request);

        $securityContext = $this->container->get('security.context');
        if(!TodoList::isAccessable($securityContext, $this, $todoList, TodoList::CREATOR_ROLE)){
            return $this->render('post/access_denied.html.twig'); 
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($todoList);
            $em->flush();
        }

        return $this->redirectToRoute('todolist_index');
    }

    /**
     * Creates a form to delete a TodoList entity.
     *
     * @param TodoList $todoList The TodoList entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TodoList $todoList)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('todolist_delete', array('id' => $todoList->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}

<?php

namespace Blogger\TodolistBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\TodolistBundle\Entity\Task;
use Blogger\TodolistBundle\Entity\TodoList;
use Blogger\TodolistBundle\Form\TaskType;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * Creates a new Task entity.
     *
     * @Route("/{id}/new", name="task_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        $todoList = $this
            ->getDoctrine()
            ->getRepository('BloggerTodolistBundle:TodoList')
            ->find($id);

        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::USER_ROLE)){
            return $this->render('post/access_denied.html.twig'); 
        }

        $task = new Task();
        $form = $this->createForm('Blogger\TodolistBundle\Form\TaskType', $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $todoList->addTask($task);
            $em->flush();

            return $this->redirectToRoute('todolist_show', array('id' => $id));
        }

        return $this->render('task/new.html.twig', array(
            'task' => $task,
            'form' => $form->createView(),
            'list' => $id
        ));
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/{id}", name="task_show")
     * @Method("GET")
     */
    public function showAction(Task $task)
    {
        $todoList = $task->getTodolist();
        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::USER_ROLE)){
            return $this->render('post/access_denied.html.twig'); 
        }

        $deleteForm = $this->createDeleteForm($task);

        return $this->render('task/show.html.twig', array(
            'task' => $task,
            'list' => $task->getTodolist()->getId(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Task $task)
    {
        $todoList = $task->getTodolist();
        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::USER_ROLE)){
            return $this->render('post/access_denied.html.twig');
        }

        $deleteForm = $this->createDeleteForm($task);
        $editForm = $this->createForm('Blogger\TodolistBundle\Form\TaskType', $task);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('todolist_show', array('id' => $task->getTodolist()->getId()));
        }

        return $this->render('task/edit.html.twig', array(
            'task' => $task,
            'list' => $task->getTodolist()->getId(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Task $task)
    {

        $todoList = $task->getTodolist();
        $securityContext = $this->container->get('security.context');
        if(!$todoList->isAccessable($securityContext, $this, TodoList::USER_ROLE)){
            return $this->render('post/access_denied.html.twig'); 
        }

        $id = $todoList->getId();
        $form = $this->createDeleteForm($task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('todolist_show', array('id' => $id));
    }

    /**
     * Creates a form to delete a Task entity.
     *
     * @param Task $task The Task entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Task $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('task_delete', array('id' => $task->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}

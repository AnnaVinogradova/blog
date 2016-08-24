<?php

namespace Blogger\ChatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Blogger\ChatBundle\Entity\Chat;
use Blogger\ChatBundle\Form\ChatType;

/**
 * Chat controller.
 *
 * @Route("/chat")
 */
class ChatController extends Controller
{
    /**
     * Lists all Chat entities.
     *
     * @Route("/", name="chat_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $chats = $em->getRepository('BloggerChatBundle:Chat')->findAll();

        return $this->render('chat/index.html.twig', array(
            'chats' => $chats,
        ));
    }

    /**
     * Creates a new Chat entity.
     *
     * @Route("/new", name="chat_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $chat = new Chat();
        $form = $this->createForm('Blogger\ChatBundle\Form\ChatType', $chat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $securityContext = $this->container->get('security.context');
            $user = $securityContext->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            $chat->setUser($user);
            $em->persist($chat);
            $em->flush();

            return $this->redirectToRoute('chat_show', array('id' => $chat->getId()));
        }

        return $this->render('chat/new.html.twig', array(
            'chat' => $chat,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Chat entity.
     *
     * @Route("/{id}", name="chat_show")
     * @Method("GET")
     */
    public function showAction(Chat $chat)
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('BloggerChatBundle:ChatMessage');
            $messages = $repo->findBy( 
                array('chat' => $chat->getId()),
                array('time' => 'DESC')
            );

            return $this->render('chat/show.html.twig', array(
                'chat' => $chat,
                'messages' => $messages,
            ));
        }
        throw new AccessDeniedException();
    }

    /**
     * Displays a form to edit an existing Chat entity.
     *
     * @Route("/{id}/edit", name="chat_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Chat $chat)
    {
        $deleteForm = $this->createDeleteForm($chat);
        $editForm = $this->createForm('Blogger\ChatBundle\Form\ChatType', $chat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($chat);
            $em->flush();

            return $this->redirectToRoute('chat_edit', array('id' => $chat->getId()));
        }

        return $this->render('chat/edit.html.twig', array(
            'chat' => $chat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Chat entity.
     *
     * @Route("/{id}", name="chat_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Chat $chat)
    {
        $form = $this->createDeleteForm($chat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($chat);
            $em->flush();
        }

        return $this->redirectToRoute('chat_index');
    }

    /**
     * Creates a form to delete a Chat entity.
     *
     * @param Chat $chat The Chat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Chat $chat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('chat_delete', array('id' => $chat->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

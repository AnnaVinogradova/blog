<?php

namespace Blogger\GameBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Blogger\GameBundle\Entity\Game;
use Blogger\GameBundle\Form\GameType;
use Symfony\Component\Form\FormError;

/**
 * Game controller.
 *
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * Lists all Game entities.
     *
     * @Route("/", name="game_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('BloggerGameBundle:Game');
            $mygames = array();
            $answers = array();

            $mygames = $repo->findBy(
                array("player1" => $user)
                );

            $requests = $repo->findBy(
                array("player2" => $user)
                );

            foreach ($requests as $game) {
                if($game->getNumber2() != null){
                    $mygames[] = $game;
                } else {
                     $answers[] = $game;
                }
            }

            return $this->render('game/index.html.twig', array(
                'mygames' => $mygames,
                "requests" => $answers
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Creates a new Game entity.
     *
     * @Route("/new", name="game_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();

            $game = new Game();
            $form = $this->createForm('Blogger\GameBundle\Form\GameType', $game);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $player2 = $game->getPlayer2();
                if($user != $player2){
                    $repo = $em->getRepository('BloggerGameBundle:Game');
                    $isExists = $repo->findBy(
                        array("player1" => $user,
                              "player2" => $player2)
                        );
                    if(!$isExists){
                        $game->setPlayer1($user);
                        $game->setNextStep($user);
                        $em->persist($game);
                        $em->flush();

                        return $this->redirectToRoute('game_index');
                    } else {
                        $form->addError(new FormError("This game already exists."));
                    }
                } else {
                    $form->addError(new FormError("You can't play with yourself."));
                }
                
            }

            return $this->render('game/new.html.twig', array(
                'game' => $game,
                'form' => $form->createView(),
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Creates a new Game entity.
     *
     * @Route("/play/{id}", name="play_game")
     * @Method({"GET"})
     */
    public function playAction($id)
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $game = $em->getRepository('BloggerGameBundle:Game')->findOneById($id);
            if(!$game){
                throw new NotFoundHttpException("Page not found");                
            }
            if(($user != $game->getPlayer2()) && ($user != $game->getPlayer1())){
                    throw new AccessDeniedException();
                }

            if($game->getPlayer1() == $user){
                $number = $game->getNumber1();
            } else {
                $number = $game->getNumber2();
            }

            return $this->render('game/play.html.twig', array(
                'game' => $game,
                'number' => $number
            ));
        }
        throw new AccessDeniedException();
    }

    /**
     * Displays a form to edit an existing Game entity.
     *
     * @Route("/{id}/edit", name="game_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Game $game)
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $deleteForm = $this->createDeleteForm($game);
            $editForm = $this->createForm('Blogger\GameBundle\Form\GameEditType', $game);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $number = $game->getNumber2();
                $game = $em->getRepository('BloggerGameBundle:Game')->findOneById($game->getId());

                if($user != $game->getPlayer2()){
                    throw new AccessDeniedException();
                }

                $game = $game->setNumber2($number);
                $em->persist($game);
                $em->flush();

                return $this->redirectToRoute('game_edit', array('id' => $game->getId()));
            }

            return $this->render('game/edit.html.twig', array(
                'game' => $game,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        }

        throw new AccessDeniedException();
    }

    /**
     * Deletes a Game entity.
     *
     * @Route("/{id}", name="game_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Game $game)
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $form = $this->createDeleteForm($game);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if(($user != $game->getPlayer2()) && ($user != $game->getPlayer1())){
                    throw new AccessDeniedException();
                }
                $em->remove($game);
                $em->flush();
            }

            return $this->redirectToRoute('game_index');
        }

        throw new AccessDeniedException();
    }

    /**
     * Creates a form to delete a Game entity.
     *
     * @param Game $game The Game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Game $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('game_delete', array('id' => $game->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

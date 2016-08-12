<?php

namespace Blogger\WallBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Blogger\WallBundle\Entity\Wall;
use Blogger\WallBundle\Entity\WallPost;
use Blogger\WallBundle\Form\WallPostType;

/**
 * WallPost controller.
 *
 * @Route("/wall")
 */
class WallPostController extends Controller
{
    /**
     * Lists all WallPost entities.
     *
     * @Route("/", name="wallpost_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $yourPosts = array();
            $allPosts = array();
            $wall = null;

            if(! $wall = $user->getWall()){
                $wall = new Wall();
                $wall->setUser($user);
                $em->persist($wall);
                $em->flush();
            } else {
                $yourPosts = $em->getRepository('BloggerWallBundle:WallPost')->findBy( 
                    array('wall' => $wall,
                    'user' => $user)
                );
                $allPosts = $wall->getPosts();
                foreach ($allPosts as $post) {
                    $post->form = $this->createDeleteForm($post)->createView();
                }
            }           
           
            return $this->render('wallpost/index.html.twig', array(
                'wall_name' => 'Your wall',
                'id' => $wall->getId(),
                'all_posts' => $allPosts,
                'your_posts' => $yourPosts
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Lists all WallPost entities.
     *
     * @Route("/users", name="wall_users")
     * @Method("GET")
     */
    public function userWallAction()
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $allWalls = $em->getRepository('BloggerWallBundle:Wall')->findAll();                          
           
            return $this->render('wallpost/walls.html.twig', array(
                'walls' => $allWalls
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Lists all WallPost entities.
     *
     * @Route("/{id}", name="wall_index")
     * @Method("GET")
     */
    public function wallAction($id)
    {
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $user = $securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $yourPosts = array();
            $allPosts = array();

            $wall = $em->getRepository('BloggerWallBundle:Wall')->findOneBy(
                    array('id' => $id)
                );
            
            if($user == $wall->getUser()){
                return $this->redirectToRoute('wallpost_index');
            }

            $yourPosts = $em->getRepository('BloggerWallBundle:WallPost')->findBy( 
                    array('wall' => $wall,
                    'user' => $user)
                );

            $allPosts = $wall->getPosts();                
          
           
            return $this->render('wallpost/index.html.twig', array(
                'wall_name' => $wall->getUser() . "'s wall",
                'id' => $wall->getId(),
                'all_posts' => $allPosts,
                'your_posts' => $yourPosts,
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Creates a new WallPost entity.
     *
     * @Route("/{id}/new", name="wallpost_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $wall = $em->getRepository('BloggerWallBundle:Wall')->findOneBy(
                    array('id' => $id)
                );

        $wallPost = new WallPost();
        $form = $this->createForm('Blogger\WallBundle\Form\WallPostType', $wallPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $securityContext = $this->container->get('security.context');
            $user = $securityContext->getToken()->getUser();

            
            $wallPost->setWall($wall);
            $wallPost->setUser($user);

            $em->persist($wallPost);
            $em->flush();

            return $this->redirectToRoute('wall_index', array('id' => $id));
        }

        return $this->render('wallpost/new.html.twig', array(
            'id' => $id,
            'wallPost' => $wallPost,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a WallPost entity.
     *
     * @Route("/{id}", name="wallpost_show")
     * @Method("GET")
     */
    public function showAction(WallPost $wallPost)
    {
        $deleteForm = $this->createDeleteForm($wallPost);

        return $this->render('wallpost/show.html.twig', array(
            'wallPost' => $wallPost,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WallPost entity.
     *
     * @Route("/{id}/edit", name="wallpost_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, WallPost $wallPost)
    {
        $deleteForm = $this->createDeleteForm($wallPost);
        $editForm = $this->createForm('Blogger\WallBundle\Form\WallPostType', $wallPost);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wallPost);
            $em->flush();

            return $this->redirectToRoute('wallpost_edit', array('id' => $wallPost->getId()));
        }

        return $this->render('wallpost/edit.html.twig', array(
            'wallPost' => $wallPost,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WallPost entity.
     *
     * @Route("/{id}", name="wallpost_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, WallPost $wallPost)
    {
        $form = $this->createDeleteForm($wallPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($wallPost);
            $em->flush();
        }

        return $this->redirectToRoute('wallpost_index');
    }

    /**
     * Creates a form to delete a WallPost entity.
     *
     * @param WallPost $wallPost The WallPost entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WallPost $wallPost)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('wallpost_delete', array('id' => $wallPost->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

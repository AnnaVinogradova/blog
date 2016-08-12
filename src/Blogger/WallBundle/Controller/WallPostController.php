<?php

namespace Blogger\WallBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
     * Lists all WallPost entities on user wall.
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
     * Lists all Walls.
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
     * Lists all WallPost in user's wall.
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
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $wall = $em->getRepository('BloggerWallBundle:Wall')->findOneBy(
                        array('id' => $id)
                    );

            $wallPost = new WallPost();
            $form = $this->createForm('Blogger\WallBundle\Form\WallPostType', $wallPost);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $securityContext->getToken()->getUser();            
                $wallPost->setWall($wall);
                $wallPost->setUser($user);
                $file = $wallPost->getImg();
                    
                    if($file != null){
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();

                        $file->move(
                            $this->getParameter('post_directory'),
                            $fileName
                        );
                        $wallPost->setImg($fileName);
                    }

                $em->persist($wallPost);
                $em->flush();

                return $this->redirectToRoute('wall_index', array('id' => $id));
            }

            return $this->render('wallpost/new.html.twig', array(
                'id' => $id,
                'wallPost' => $wallPost,
                'form' => $form->createView(),
            ));
        } else {
            throw new AccessDeniedException();
        }
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
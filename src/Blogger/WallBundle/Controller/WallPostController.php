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
                $repo = $em->getRepository('BloggerWallBundle:WallPost');
                $yourPosts = $repo->findBy( 
                    array('wall' => $wall,
                    'user' => $user),
                    array('date' => 'DESC')
                );
                $allPosts = $repo->findBy( 
                    array('wall' => $wall),
                    array('date' => 'DESC')
                );

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

            $friend_request = $em->getRepository('BloggerWallBundle:FriendRequest')->findOneBy(
                    array('sender' => $user,
                          'receiver' => $wall->getUser())
                );

            if($friend_request){
                if(!$friend_request->getStatus()){
                    return $this->render('wallpost/request.html.twig', array(
                        'message' => 'You sent user your request. Now you should waiting',
                        'link' => false,
                        'id' => $id,
                    ));
                }
            } else {
                return $this->render('wallpost/request.html.twig', array(
                        'message' => 'This user not in your friendlist. You can send request to user.',
                        'link' => true,
                        'id' => $id,
                    ));
            }

            $repo =  $em->getRepository('BloggerWallBundle:WallPost');
            $yourPosts =$repo->findBy( 
                    array('wall' => $wall,
                    'user' => $user),
                    array('date' => 'DESC')
                );
            $allPosts = $repo->findBy( 
                    array('wall' => $wall),
                    array('date' => 'DESC')
                ); 

            if($securityContext->isGranted('ROLE_ADMIN')){                  
                foreach ($allPosts as $post) {
                        $post->form = $this->createDeleteForm($post)->createView();
                    }
            }

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
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('ROLE_USER')){
            $form = $this->createDeleteForm($wallPost);
            $form->handleRequest($request);

            $user = $securityContext->getToken()->getUser(); 
            $owner = $wallPost->getWall()->getUser();

            if (($user != $owner) && (!$securityContext->isGranted('ROLE_ADMIN'))){
                throw new AccessDeniedException();
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($wallPost);
                $em->flush();
            }

            return $this->redirectToRoute('wallpost_index');
        } else {
            throw new AccessDeniedException();
        }
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

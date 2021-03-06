<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Blogger\BlogBundle\Entity\Post;
use Blogger\BlogBundle\Form\PostType;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities for current user.
     *
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $securityContext->getToken()->getUser();

            if($securityContext->isGranted('ROLE_ADMIN')){
                $posts = $em->getRepository('BloggerBlogBundle:Post')->findBy(
                    array(), 
                    array('created' => 'DESC'));

                $myPosts = $em->getRepository('BloggerBlogBundle:Post')->findBy(
                    array('user' => $user), 
                    array('created' => 'DESC'));

                return $this->render('post/index.html.twig', array(
                'posts' => $posts,
                'my_posts' => $myPosts
                ));
                
            } else {    
                $posts = $em->getRepository('BloggerBlogBundle:Post')->findBy(
                    array('user' => $user), 
                    array('created' => 'DESC'));
            
            return $this->render('post/index.html.twig', array(
                'posts' => $posts,
            ));
            }
        } else {
             throw new AccessDeniedException();
        }
    }

    /**
     * Creates a new Post entity.
     *
     */
    public function newAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $user = $securityContext->getToken()->getUser();

        if($securityContext->isGranted('ROLE_USER')){
            $post = new Post();
            $form = $this->createForm('Blogger\BlogBundle\Form\PostType', $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $file = $post->getImage();
                
                if($file == null){
                    $fileName = 'default.jpg';
                } else {
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    $file->move(
                        $this->getParameter('post_directory'),
                        $fileName
                    );
                }

                $post->setImage($fileName);

                $em = $this->getDoctrine()->getManager();

                $user = $this->container->get('security.context')->getToken()->getUser();
                $user->addPost($post);
                $em->flush();

                return $this->redirectToRoute('post_show', array('id' => $post->getId()));
            }

            return $this->render('post/new.html.twig', array(
                'post' => $post,
                'form' => $form->createView(),
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction(Post $post)
    {
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getManager();

        if(! $securityContext->isGranted('ROLE_ADMIN')){
            if(! $this->checkAccess($post->getId())){
                throw new AccessDeniedException();
            }
        }

        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getManager();

        $fileName = $post->getImage();
        if(! $securityContext->isGranted('ROLE_ADMIN')){
            if(! $this->checkAccess($post->getId())){
                throw new AccessDeniedException();
            }
        }

        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('Blogger\BlogBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $file = $post->getImage();
            if($file != null){
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('post_directory'),
                    $fileName
                );
            }
            
            $post->setImage($fileName);
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getManager();

        if(! $securityContext->isGranted('ROLE_ADMIN')){
            if(! $this->checkAccess($post->getId())){
                throw new AccessDeniedException();
            }
        }

        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

     /**
     * Read post.
     *
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('BloggerBlogBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find required post.');
        }

        return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
            'post'      => $post,
        ));
    }

    /**
     * Creates a form to delete a Post entity.
     *
     * @param Post $post The Post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function checkAccess($id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        return  $em->getRepository('BloggerBlogBundle:Post')->findBy( 
                array('id' => $id,
                'user' => $user)
                );
    }

}

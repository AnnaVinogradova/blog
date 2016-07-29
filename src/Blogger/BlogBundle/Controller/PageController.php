<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
     public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getManager();

        $posts = $em->getRepository('BloggerBlogBundle:Post')
                    ->getLatestPosts();

        return $this->render('BloggerBlogBundle:Page:index.html.twig', array(
            'posts' => $posts
        ));
    }

     public function aboutAction()
    {
        return $this->render('BloggerBlogBundle:Page:about.html.twig');
    }
    
}
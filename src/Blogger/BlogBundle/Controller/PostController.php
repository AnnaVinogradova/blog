<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostController extends Controller
{
    public function showAction($id)
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
}
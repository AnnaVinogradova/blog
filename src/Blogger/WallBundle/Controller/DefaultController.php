<?php

namespace Blogger\WallBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BloggerWallBundle:Default:index.html.twig');
    }
}

<?php

namespace Blogger\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BloggerMapBundle:Default:index.html.twig');
    }
}

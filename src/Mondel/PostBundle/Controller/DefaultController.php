<?php

namespace Mondel\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MondelPostBundle:Default:index.html.twig', array('name' => $name));
    }
}

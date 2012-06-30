<?php

namespace Mondel\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function showStaticPageAction($page)
    {
    	$translate = array('ayuda' => 'help');

        return $this->render('MondelSiteBundle:Default:' . $translate[$page] . '.html.twig');
    }
}

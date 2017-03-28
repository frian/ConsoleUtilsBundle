<?php

namespace Frian\UtilsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FrianUtilsBundle:Default:index.html.twig');
    }
}

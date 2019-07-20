<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("", name="default_index")
     */
    public function defaultIndex()
    {
        return $this->render('default/index.html.twig');
    }
}

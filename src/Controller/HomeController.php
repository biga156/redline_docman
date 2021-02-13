<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Route("/home", name="home")
     * @Route("/", name="index")
     */
    public function index()
    {
        $this->addFlash('notice', 'Attention! The site is currently in beta and is constantly being developed.
        Errors can happen all the time, but I try to leave them operational after work :)');
      
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}

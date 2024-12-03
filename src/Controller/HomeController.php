<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController

{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'message' => 'Bienvenue sur la page Index !',
        ]);
    }


    #[Route('/register', name: 'app_register')]

    public function signe():Response
    {
        return $this->render('registration/register.html.twig');
    }

    #[Route('/login', name: 'app_login')]

    public function login():Response
    {
        return $this->render('security/login.html.twig');
    }

}
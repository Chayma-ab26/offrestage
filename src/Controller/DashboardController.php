<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
#[Route('/dashboard', name: 'app_dashboard')]
public function index(): Response
{
// Vérifier si l'utilisateur a bien le rôle 'ROLE_ENTREPRISE'
if ($this->isGranted('ROLE_ENTREPRISE')) {
// Si l'utilisateur est une entreprise, afficher la page dashboard
return $this->render('dashboard/index.html.twig');
}

// Si l'utilisateur n'a pas le bon rôle, redirection vers une autre page
return $this->redirectToRoute('app_login');
}
}

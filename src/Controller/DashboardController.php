<?php
namespace App\Controller;

use App\Repository\OffreStageRepository; // Import du repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
#[Route('/dashboard', name: 'app_dashboard')]
public function index(OffreStageRepository $offreStageRepository): Response
{
// Récupérer le nombre d'offres de stage
$nombreOffres = $offreStageRepository->countOffres();

// Vérifier si l'utilisateur a bien le rôle 'ROLE_ENTREPRISE'
if ($this->isGranted('ROLE_ENTREPRISE')) {
// Si l'utilisateur est une entreprise, afficher la page dashboard avec les données
return $this->render('dashboard/index.html.twig', [
'nombreOffres' => $nombreOffres // Passer le nombre d'offres au template
]);
}

// Si l'utilisateur n'a pas le bon rôle, redirection vers une autre page
return $this->redirectToRoute('app_login');
}
}

<?php
namespace App\Controller;

use App\Repository\OffreStageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DashboardEtudController extends AbstractController
{
#[Route('/etudiant/dashboard', name: 'app_etudiant_dashboard')]
public function index(OffreStageRepository $offreStageRepository): Response
{
// Récupérer le nombre d'offres de stage disponibles
$nombreOffres = $offreStageRepository->countOffres();

// Vérifier si l'utilisateur a bien le rôle 'ROLE_ETUDIANT'
if ($this->isGranted('ROLE_ETUDIANT')) {
// Passer les données au template Twig
return $this->render('dashboard/dashboardetud.html.twig', [
'nombreOffres' => $nombreOffres
]);
}

// Si l'utilisateur n'a pas le bon rôle, redirection vers la page de connexion
return $this->redirectToRoute('app_login');
}
}

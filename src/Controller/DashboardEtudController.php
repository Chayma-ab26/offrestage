<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Repository\OffreStageRepository;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardEtudController extends AbstractController
{

    #[Route('/etudiant/dashboard', name: 'app_etudiant_dashboard')]
public function index(
OffreStageRepository $offreStageRepository,
CandidatureRepository $candidatureRepository,
NotificationRepository $notificationRepository,
UserInterface $user // Injecte l'utilisateur connecté
): Response {
// Vérifier si l'utilisateur a bien le rôle 'ROLE_ETUDIANT'
if (!$this->isGranted('ROLE_ETUDIANT')) {
return $this->redirectToRoute('app_login');
}

// Récupérer le nombre d'offres de stage disponibles
$nombreOffres = $offreStageRepository->count([]);

// Récupérer le nombre de candidatures soumises par l'étudiant connecté
$candidatureCount = $candidatureRepository->count(['etudiant' => $user]);
    $unreadNotifications = $notificationRepository->findBy(['destinataire' => $user, 'lue' => 0]);

// Passer les données au template Twig
return $this->render('dashboard/dashboardetud.html.twig', [
'nombreOffres' => $nombreOffres,
'candidatureCount' => $candidatureCount,
    'unreadNotifications' => $unreadNotifications,

]);
}
}

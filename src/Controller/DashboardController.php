<?php
namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Repository\OffreStageRepository;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractController
{
    private $candidatureRepository;

    // Injection de dépendance pour le repository Candidature
    public function __construct(CandidatureRepository $candidatureRepository)
    {
        $this->candidatureRepository = $candidatureRepository;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(OffreStageRepository $offreStageRepository,
                          NotificationRepository $notificationRepository,
                          UserInterface $user // Injecte l'utilisateur connecté

    ): Response
    {

        // Vérifier si l'utilisateur a le rôle 'ROLE_ENTREPRISE'
        if (!$this->isGranted('ROLE_ENTREPRISE')) {
            // Si l'utilisateur n'a pas le bon rôle, redirection vers la page de login
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le nombre d'offres de stage
        $nombreOffres = $offreStageRepository->countOffres();  // Vous devez avoir une méthode countOffres dans le repository
        $offres = $offreStageRepository->findAll();  // Liste de toutes les offres
        $unreadNotifications = $notificationRepository->findBy(['destinataire' => $user, 'lue' => 0]);

        // Récupérer le nombre de candidatures acceptées
        $acceptedCount = $this->candidatureRepository->countAccepted();

        // Récupérer le nombre de candidatures soumises
        $nombreCandidatures = $this->candidatureRepository->countSubmittedCandidatures(); // Méthode countSubmittedCandidatures dans CandidatureRepository
        $candidaturesByMonth = $this->candidatureRepository->countCandidaturesByMonth();
        $months = [];
        $counts = [];
        foreach ($candidaturesByMonth as $data) {
            $months[] = sprintf('%02d-%d', $data['month'], $data['year']);
            $counts[] = $data['count'];
        }
        // Rendre la page dashboard avec les données
        return $this->render('dashboard/index.html.twig', [
            'nombreOffres' => $nombreOffres,
            'offres' => $offres,
            'acceptedCount' => $acceptedCount, // Passer le nombre de candidatures acceptées
            'nombreCandidatures' => $nombreCandidatures ,// Passer le nombre de candidatures soumises
            'months' => json_encode($months),
            'counts' => json_encode($counts),
            'unreadNotifications' => $unreadNotifications,

        ]);
    }
    public function viewNotifications(NotificationRepository $notificationRepository): Response
    {
        // Récupérer l'entreprise de l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        }

        // Récupérer les notifications non lues pour l'entreprise
        $notifications = $notificationRepository->findBy([
            'destinataire' => $user->getEntreprise(), // Filtrer par entreprise
            'lue' => 0, // Filtrer par notifications non lues
        ]);

        // Passer les notifications à la vue
        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

}

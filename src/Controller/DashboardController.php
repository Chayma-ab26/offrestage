<?php
namespace App\Controller;

use App\Repository\OffreStageRepository;
use App\Repository\CandidatureRepository; // Assurez-vous que cette importation est présente
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    private $candidatureRepository;

    // Injection de dépendance pour le repository Candidature
    public function __construct(CandidatureRepository $candidatureRepository)
    {
        $this->candidatureRepository = $candidatureRepository;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(OffreStageRepository $offreStageRepository): Response
    {
        // Vérifier si l'utilisateur a le rôle 'ROLE_ENTREPRISE'
        if (!$this->isGranted('ROLE_ENTREPRISE')) {
            // Si l'utilisateur n'a pas le bon rôle, redirection vers la page de login
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le nombre d'offres de stage
        $nombreOffres = $offreStageRepository->countOffres();  // Vous devez avoir une méthode countOffres dans le repository
        $offres = $offreStageRepository->findAll();  // Liste de toutes les offres
        $offres = $offreStageRepository->findAllWithUserDetails();  // Si vous avez une méthode personnalisée pour récupérer les offres avec les détails de l'utilisateur

        // Récupérer le nombre de candidatures soumises
        $nombreCandidatures = $this->candidatureRepository->countSubmittedCandidatures(); // Vous devez avoir une méthode countSubmittedCandidatures dans le CandidatureRepository

        // Rendre la page dashboard avec les données
        return $this->render('dashboard/index.html.twig', [
            'nombreOffres' => $nombreOffres,  // Passer le nombre d'offres au template
            'offres' => $offres,              // Passer la liste des offres au template
            'nombreCandidatures' => $nombreCandidatures // Passer le nombre de candidatures au template
        ]);
    }
}

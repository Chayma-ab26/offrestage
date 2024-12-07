<?php
namespace App\Controller;

use App\Entity\Candidature;
use App\Form\CandidatureType;
use App\Repository\OffreStageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/offres')]
class OffreStagePostulerController extends AbstractController
{
// Méthode pour afficher les offres de stage
#[Route('/liste', name: 'app_offre_stage_liste', methods: ['GET'])]
public function index(OffreStageRepository $offreStageRepository): Response
{
$offres = $offreStageRepository->findAll();

return $this->render('candidature/liste_offres.html.twig', [
'offres' => $offres,
]);
}

// Méthode pour postuler à une offre spécifique
    #[Route('/{id}/postuler', name: 'app_postuler_offre_stage', methods: ['GET', 'POST'])]
    public function postuler(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        OffreStageRepository $offreStageRepository
    ): Response {
        // 1. Récupérer l'offre de stage à partir de son ID
        $offreStage = $offreStageRepository->find($id);

        if (!$offreStage) {
            throw $this->createNotFoundException('L\'offre de stage n\'existe pas.');
        }

        // 2. Récupérer l'étudiant connecté
        $etudiant = $this->getUser(); // Suppose que l'étudiant est l'utilisateur connecté

        if (!$etudiant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour postuler.');
        }

        // 3. Définir le statut de candidature
        $status = 'En attente'; // Par exemple, un statut par défaut
        // Si vous avez une table pour les statuts, récupérez-le depuis le repository :
        // $status = $statusRepository->findOneBy(['name' => 'En attente']);

        // 4. Créer et remplir une nouvelle candidature
        $candidature = new Candidature();
        $candidature->setEtudiant($etudiant);
        $candidature->setOffreStage($offreStage);
        $candidature->setStatus($status);
        $candidature->setDateSoumission(new \DateTime()); // Ajouter une date actuelle automatiquement

        // Gestion des fichiers (CV et lettre de motivation)
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des fichiers uploadés
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $cvFilename = uniqid() . '.' . $cvFile->guessExtension();
                $cvFile->move($this->getParameter('uploads_directory'), $cvFilename);
                $candidature->setCv($cvFilename);
            }

            $lettreFile = $form->get('lettreDeMotivation')->getData();
            if ($lettreFile) {
                $lettreFilename = uniqid() . '.' . $lettreFile->guessExtension();
                $lettreFile->move($this->getParameter('uploads_directory'), $lettreFilename);
                $candidature->setLettreDeMotivation($lettreFilename);
            }

            // Enregistrer la candidature
            $entityManager->persist($candidature);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature a été soumise avec succès !');
            return $this->redirectToRoute('app_offre_stage_liste');
        }

        // Rendre le formulaire de candidature
        return $this->render('candidature/candidater_offre.html.twig', [
            'form' => $form->createView(),
            'offreStage' => $offreStage,
        ]);
    }

}

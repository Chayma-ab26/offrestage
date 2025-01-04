<?php

namespace App\Controller;

use App\Entity\OffreStage;
use App\Form\OffreStageType;
use App\Repository\OffreStageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/offre/stage')]
final class OffreStageController extends AbstractController
{
    #[Route(name: 'app_offre_stage_index', methods: ['GET', 'POST'])]
    public function index(Request $request, OffreStageRepository $offreStageRepository, EntityManagerInterface $entityManager): Response
    {
        // Si une mise à jour de statut est demandée
        if ($request->isMethod('POST')) {
            $offreStageId = $request->request->get('id');
            $status = $request->request->get('status');

            // Récupérer l'offre de stage à mettre à jour
            $offreStage = $offreStageRepository->find($offreStageId);

            if ($offreStage && in_array($status, ['ouverte', 'fermée', 'en cours de sélection'])) {
                // Mettre à jour le statut
                $offreStage->setStatus($status);

                $entityManager->flush(); // Sauvegarder les modifications en base de données

                $this->addFlash('success', 'Le statut de l\'offre a été mis à jour avec succès.');
            } else {
                $this->addFlash('error', 'Erreur : L\'offre de stage est introuvable ou le statut est invalide.');
            }

            return $this->redirectToRoute('app_offre_stage_index');
        }

        $secteur = $request->query->get('secteuractivite', null);

        // Si un secteur est sélectionné, filtrer les offres
        if ($secteur) {
            $offres = $offreStageRepository->findBySecteuractivite($secteur); // Utiliser la méthode modifiée
        } else {
            // Sinon, récupérer toutes les offres
            $offres = $offreStageRepository->findAll();
        }

        return $this->render('candidature/liste_offres.html.twig', [
            'offres' => $offres,  // Passer les résultats filtrés ou non
            'secteur' => $secteur,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_stage_show', methods: ['GET'])]
    public function show(OffreStage $offreStage): Response
    {
        return $this->render('offre_stage/show.html.twig', [
            'offre_stage' => $offreStage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_stage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OffreStage $offreStage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreStageType::class, $offreStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_stage/edit.html.twig', [
            'offre_stage' => $offreStage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_stage_delete', methods: ['POST'])]
    public function delete(Request $request, OffreStage $offreStage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offreStage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offreStage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);
    }


}

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
    #[Route('/offre/stage', name: 'app_offre_stage_index', methods: ['GET', 'POST'])]
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

            // Redirection après la mise à jour
            return $this->redirectToRoute('app_offre_stage_index');
        }


            $offres = $offreStageRepository->findAll();


        // Rendre le template 'offre_stage/index.html.twig'
        return $this->render('offre_stage/index.html.twig', [
            'offres' => $offres,  // Passer les résultats filtrés ou non
        ]);
    }
    #[Route('/etudiant', name: 'app_offre_stage_etudiant_index', methods: ['GET', 'POST'])]
    public function indexEtudiant(Request $request, OffreStageRepository $offreStageRepository): Response
    {
        // Récupérer le secteur sélectionné dans la requête (si présent)
        $secteur = $request->query->get('secteuractivite', null);

        // Si un secteur est sélectionné, filtrer les offres de stage
        if ($secteur) {
            $offres = $offreStageRepository->findBySecteuractivite($secteur);
        } else {
            // Sinon, récupérer toutes les offres de stage
            $offres = $offreStageRepository->findAll();
        }

        // Rendre le template 'offre_stage/etudiant_index.html.twig'
        return $this->render('candidature/liste_offres.html.twig', [
            'offres' => $offres, // Passer les résultats filtrés ou non
            'secteur' => $secteur, // Passer la valeur du secteur si présente
        ]);
    }


    #[Route('/new', name: 'app_offre_stage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offreStage = new OffreStage();

        $form = $this->createForm(OffreStageType::class, $offreStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offreStage);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle offre de stage ajoutée avec succès.');

            return $this->redirectToRoute('app_offre_stage_index');
        }

        return $this->render('offre_stage/new.html.twig', [
            'form' => $form->createView(),
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

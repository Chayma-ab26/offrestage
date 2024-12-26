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
            }
        }

        return $this->render('offre_stage/index.html.twig', [
            'offre_stages' => $offreStageRepository->findAll(),
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

            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_stage/new.html.twig', [
            'offre_stage' => $offreStage,
            'form' => $form,
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

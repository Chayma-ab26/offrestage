<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/candidature')]
final class CandidatureController extends AbstractController
{
    #[Route(name: 'app_candidature_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        CandidatureRepository $candidatureRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer toutes les candidatures
        $candidatures = $candidatureRepository->findAllWithRelations();

        // Gestion du formulaire de changement de statut
        if ($request->isMethod('POST')) {
            $candidatureId = $request->request->get('candidature_id');
            $newStatus = $request->request->get('status');

            if ($candidatureId && $newStatus) {
                $candidature = $candidatureRepository->find($candidatureId);

                if ($candidature) {
                    $candidature->setStatus($newStatus);
                    $entityManager->flush();

                    $this->addFlash('success', 'Le statut de la candidature a été mis à jour avec succès.');
                } else {
                    $this->addFlash('danger', 'Candidature non trouvée.');
                }
            } else {
                $this->addFlash('danger', 'Données de formulaire invalides.');
            }

            return $this->redirectToRoute('app_candidature_index');
        }
        foreach ($candidatures as $candidature) {
            if (!$candidature->getOffreStage()) {
                dump($candidature->getId());
            }
        }

        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }


    #[Route('/new', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }#[Route('/mes-candidatures', name: 'app_mes_candidatures')]
public function mesCandidatures(CandidatureRepository $candidatureRepository): Response
{
    // Obtenir l'utilisateur connecté
    $user = $this->getUser(); // Récupère l'utilisateur connecté

    // Vérifier que l'utilisateur est connecté
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }

    // Vérifier que l'utilisateur a le rôle d'étudiant
    if (!in_array('ROLE_ETUDIANT', $user->getRoles())) {
        throw $this->createAccessDeniedException('Seuls les étudiants peuvent voir cette page.');
    }

    // Récupérer les candidatures de l'utilisateur connecté
    $candidatures = $candidatureRepository->findBy(['user' => $user]);

    // Renvoyer la vue Twig avec les candidatures
    return $this->render('candidature/mes_candidatures.html.twig', [
        'candidatures' => $candidatures,
    ]);
}


}

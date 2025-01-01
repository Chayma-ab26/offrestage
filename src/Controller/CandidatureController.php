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
        Request                $request,
        CandidatureRepository  $candidatureRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
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

    #[Route('/mes-candidatures', name: 'app_mes_candidatures', methods: ['GET'])]
    public function mesCandidatures(CandidatureRepository $candidatureRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérification : l'utilisateur doit être connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur est un étudiant
        if (!in_array('ROLE_ETUDIANT', $user->getRoles())) {
            throw $this->createAccessDeniedException('Seuls les étudiants peuvent consulter leurs candidatures.');
        }


        // Récupérer les candidatures envoyées par l'étudiant
        $candidatures = $candidatureRepository->findBy(['etudiant' => $user]);

        // Renvoyer une réponse avec les candidatures
        return $this->render('candidature/mes_candidatures.html.twig', [
            'candidatures' => $candidatures
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
        if ($this->isCsrfTokenValid('delete' . $candidature->getId(), $request->request->get('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();

            // Ajout d'un message de succès
            $this->addFlash('success', 'La candidature a été supprimée avec succès.');
        } else {
            // Ajout d'un message d'erreur si le token CSRF est invalide
            $this->addFlash('danger', 'Le token CSRF est invalide. La suppression a échoué.');
        }

        return $this->redirectToRoute('app_mes_candidatures', [], Response::HTTP_SEE_OTHER);
    }
 #[Route('/candidature/update/{id}', name: 'update_candidature_status')]
public function updateStatus($id, Request $request, NotificationRepository $notificationRepository)
{
    // Trouver la candidature par son ID
    $candidature = // Votre logique pour récupérer la candidature

        // Mettre à jour le statut de la candidature
        $candidature->setStatus('accepté'); // Exemple de changement de statut

    // Enregistrer la notification pour l'étudiant
    $notification = new Notification();
    $notification->setMessage('Votre candidature a changé de statut.')
        ->setUser($candidature->getEtudiant())  // L'étudiant qui est lié à la candidature
        ->setIsRead(false) // Non lue par défaut
        ->setDateEnvoi(new \DateTime());

    // Sauvegarder la notification
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($notification);
    $entityManager->flush();

    return $this->redirectToRoute('app_candidature_status'); // Rediriger après la mise à jour
}

}
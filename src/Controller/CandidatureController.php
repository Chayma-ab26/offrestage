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
    public function updateCandidatureStatus($id, Request $request)
    {
        // Récupérer la candidature par ID
        $candidature = $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->find($id);

        if (!$candidature) {
            throw $this->createNotFoundException('Candidature introuvable.');
        }

        // Mettre à jour le statut (exemple : "accepté")
        $newStatus = $request->request->get('status'); // Nouveau statut
        $candidature->setStatus($newStatus);

        // Créer une notification pour l'étudiant
        $notification = new Notification();
        $notification->setMessage("Votre candidature est désormais : $newStatus.")
            ->setDestinataire($candidature->getEtudiant()) // Associer l'étudiant
            ->setDateEnvoi(new \DateTime())
            ->setIsRead(false); // Non lue par défaut

        // Sauvegarder les changements
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->redirectToRoute('candidature_list'); // Redirection après mise à jour
    }

    public function submitCandidature(Request $request)
    {
        // Création d'une nouvelle candidature
        $candidature = new Candidature();
        $candidature->setDetails($request->request->get('details'))
            ->setEntreprise($this->getUser()->getEntreprise())  // Assurez-vous d'avoir l'entreprise de l'utilisateur
            ->setDateSoumission(new \DateTime());

        // Sauvegarder la candidature
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($candidature);
        $entityManager->flush();

        // Créer une notification pour l'entreprise
        $notification = new Notification();
        $notification->setMessage("Une nouvelle candidature a été soumise.")
            ->setDestinataire($candidature->getEntreprise()) // Associer l'entreprise à la notification
            ->setDateEnvoi(new \DateTime())
            ->setLue(0); // Notification non lue par défaut

        // Sauvegarder la notification
        $entityManager->persist($notification);
        $entityManager->flush();

        // Redirection ou réponse après la soumission
        return $this->redirectToRoute('candidature_success'); // Rediriger vers une page de succès
    }

}
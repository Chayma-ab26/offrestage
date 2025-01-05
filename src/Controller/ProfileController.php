<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    private $entityManager;

    // Injection du service EntityManagerInterface dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/profile', name: 'app_profile')]


    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation de l'entité manager injecté
            $this->entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

      #[Route(path: '/profile/supprimer', name: 'user_delete',methods: ['GET','POST'])]

    public function deleteProfile(Request $request, Security $security): Response
    {
        // Vérifiez le jeton CSRF pour protéger contre les attaques CSRF
        if (!$this->isCsrfTokenValid('delete_profile', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('profil_show');
        }

        // Récupérez l'utilisateur connecté
        $user = $security->getUser();

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_login');
        }

        // Supprimez l'utilisateur
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Déconnectez l'utilisateur après la suppression
        $this->addFlash('success', 'Votre profil a été supprimé avec succès.');
        return $this->redirectToRoute('app_logout');
    }
    #[Route(path: '/profile/{id}', name: 'app_profile_show', methods: ['GET'])]
    public function showProfile(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Candidat non trouvé.');
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

}

<?php

namespace App\Controller;


use App\Repository\EtudiantRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfiletudiantController extends AbstractController
{

    #[Route('/profil/{id}',name:'etudiant_profile' , requirements: ['id' => '\d+'])]

    public function profile(int $id, UserRepository $userRepository): Response
    {
        $etudiant = $userRepository->find($id);

        if (!$etudiant) {
            throw $this->createNotFoundException('L\'étudiant n\'a pas été trouvé.');
        }

        return $this->render('Candidature/profile.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }
}

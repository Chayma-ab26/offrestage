<?php
namespace App\Controller;

use App\Entity\OffreStage;
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
#[Route('/liste', name: 'app_offre_stage_liste', methods: ['GET'])]
public function index(OffreStageRepository $offreStageRepository): Response
{
// Récupérer toutes les offres de stage
$offres = $offreStageRepository->findAll();

return $this->render('candidature/liste_offres.html.twig', [
'offres' => $offres,
]);
}

#[Route('/{id}/postuler', name: 'app_postuler_offre_stage', methods: ['GET', 'POST'])]
public function postuler(Request $request, EntityManagerInterface $entityManager, OffreStage $offreStage): Response
{
$candidature = new Candidature();
$candidature->setOffreStage($offreStage);
$candidature->setUtilisateur($this->getUser());

$form = $this->createForm(CandidatureType::class, $candidature);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$entityManager->persist($candidature);
$entityManager->flush();

$this->addFlash('success', 'Votre candidature a été envoyée avec succès.');
return $this->redirectToRoute('app_offre_stage_liste');
}

return $this->render('candidature/candidater_offre.html.twig', [
'form' => $form->createView(),
'offreStage' => $offreStage,
]);
}
}

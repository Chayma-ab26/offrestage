<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
final class NotificationController extends AbstractController
{

    // Récupérer l'utilisateur connecté
    #[Route('/notifications', name: 'app_notification_index')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $unreadNotifications = $notificationRepository->findUnreadNotificationsForUser($user); // Appel de la méthode définie dans le repository

        return $this->render('notification/index.html.twig', [
            'unreadNotifications' => $unreadNotifications,
        ]);
    }
    #[Route('/notification/{id}/mark-as-read', name: 'notification_mark_as_read')]
    public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $notification->setLue(1); // Marquer comme lue
        $entityManager->flush();

        return $this->redirectToRoute('app_notification_index'); // Rediriger vers la page des notifications
    }

}
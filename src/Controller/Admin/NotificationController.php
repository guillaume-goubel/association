<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use App\Service\CheckAuthorizationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/notification', name: 'admin_notification_')]
class NotificationController extends AbstractController
{

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_notification_new', null)) {
            return $this->redirectToRoute('admin_index');
        }
        
        $notification = new Notification();
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // author
            $notification->setAuthor($this->getUser());
            
            $entityManager->persist($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notification $notification, EntityManagerInterface $entityManager, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_notification_edit', null)) {
            return $this->redirectToRoute('admin_index');
        }
        
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, Notification $notification, EntityManagerInterface $entityManager, CheckAuthorizationService $checkAuthorizationService): Response
    {
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_notification_delete', null)) {
            return $this->redirectToRoute('admin_index');
        }
        
        $entityManager->remove($notification);
        $entityManager->flush();
        $this->addFlash('success', 'Opération effectuée');

        return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Entity\ActivityMessage;
use App\Form\ActivityMessageType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ActivityMessageRepository;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/activity/message', name: 'admin_activity_message_')]
class ActivityMessageController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $activityRepository, ActivityMessageRepository $activityMessageRepository): Response
    {
        $activityId = $request->query->get('activityId');

        return $this->render('admin/activity_message/index.html.twig', [
            'activityMessages' => $activityMessageRepository->findBy(['activity'=> $activityId]),
            'activity' => $activityRepository->findOneBy(['id' => $activityId])
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ActivityRepository $activityRepository): Response
        {
        $activityId = $request->query->get('activityId');
        $activity = $activityRepository->findOneBy(['id' => $activityId]);

        $activityMessage = new ActivityMessage();
        $form = $this->createForm(ActivityMessageType::class, $activityMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $activityMessage->setActivity($activity);
            $activityMessage->setUser($this->getUser());
            
            $entityManager->persist($activityMessage);
            $entityManager->flush();
            
            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_activity_message_index', ['activityId' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity_message/new.html.twig', [
            'activity_message' => $activityMessage,
            'form' => $form,
            'activity' => $activity
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActivityMessage $activityMessage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityMessageType::class, $activityMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_activity_message_index', ['activityId' => $activityMessage->getActivity()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity_message/edit.html.twig', [
            'activityMessage' => $activityMessage,
            'form' => $form,
            'activity' => $activityMessage->getActivity()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, ActivityMessage $activityMessage, EntityManagerInterface $entityManager): Response
    {
        $activityId = $activityMessage->getActivity()->getId();
        
        $entityManager->remove($activityMessage);
        $entityManager->flush();

        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_activity_message_index', ['activityId' => $activityId], Response::HTTP_SEE_OTHER);
    }
}

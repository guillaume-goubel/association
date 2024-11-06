<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/event', name: 'admin_event_')]
class EventController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $yearChoice = $request->query->get('yearChoice') ?? date("Y");
        $monthChoice = $request->query->get('monthChoice') ?? date("m");
        $creatorChoice = $request->query->get('creatorChoice') ?? $this->getUser()->getId();

        $eventList = $eventRepository->getEventListforAdmin($yearChoice, $monthChoice, $creatorChoice);

        // Distinct month / year createdAt for select
        $years = $eventRepository->getDistincYearCreatedAt();
        $months = $eventRepository->getDistinctMonthCreatedAt($yearChoice);
        $creators = $eventRepository->getDistinctCreator();

        return $this->render('admin/event/index.html.twig', [
            'events' => $eventList,
            'years' => $years,
            'months' => $months,
            'creators' => $creators,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService): Response
    {
        $event = new Event();
        $user = $this->getUser();
        $userActivityArray = [];

        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            foreach ($activityRepository->findAll() as $activity) {
                array_push($userActivityArray, $activity->getId());
            }
        }else{
            foreach ($activityRepository->findBy(["isEnabled" => 1], ['ordering' => 'ASC']) as $activity) {
                if ($activity->isActivityInUserControl($user) === true) {
                    array_push($userActivityArray, $activity->getId());
                }
            }
        }

        $form = $this->createForm(EventType::class, $event, [
            'activity_ids' => $userActivityArray, // Passe les IDs ici
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $eventService->process($event);

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('admin/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use App\Service\EventService;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/event', name: 'admin_event_')]
class EventController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository): Response
    {
        $user = $this->getUser();

        $yearChoice = $request->query->get('yearChoice') ?? date("Y");
        $monthChoice = $request->query->get('monthChoice') ?? date("m");
        $creatorChoice = $request->query->get('creatorChoice') ?? $this->getUser()->getId();
        $dateChoice = $request->query->get('creatorChoice') ?? 'dateStartAt';
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $creatorChoice = 'all';
        }
        $activityChoice = $request->query->get('activityChoice') ?? "all";

        // Distinct month / year createdAt for select
        $yearsList = $eventRepository->getDistincYearCreatedAt();
        $monthsList = $eventRepository->getDistinctMonthCreatedAt($yearChoice);
        $creatorsList = $eventRepository->getDistinctCreator();

        $activityList = $activityRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/event/index.html.twig', [
            'events' => $eventRepository->getEventListforAdmin($yearChoice, $monthChoice, $creatorChoice, $activityChoice, $dateChoice),
            'yearsList' => $yearsList,
            'monthsList' => $monthsList,
            'creatorsList' => $creatorsList,
            'activityList' => $activityList,
            'yearChoice' => $yearChoice,
            'monthChoice' => $monthChoice,
            'creatorChoice' => $creatorChoice,
            'activityChoice' => $activityChoice,
            'dateChoice' => $dateChoice,
            'isEventActionsButtonVisible' => true,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService): Response
    {
        $event = new Event();
        $user = $this->getUser();
        $userActivityArray = [];
        $activityChoice = null;
        $activityId = $request->query->get('activityId') ?? null;
        if ($activityId){
            $activityChoice = $activityRepository->findOneBy(['id' => $activityId]);
        }

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
            'activity_ids' => $userActivityArray,
            'selected_activity' => $activityChoice, // Passe les IDs ici
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $eventService->process($event);

            if(!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                $event->setUser($user);
            }
            
            $entityManager->persist($event);
            $entityManager->flush();
            
            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService): Response
    {   
        $user = $this->getUser();
        
        // Vérification si l'ID de l'utilisateur connecté correspond à l'ID de l'utilisateur de l'événement
        if ($user->getId() !== $event->getUser()->getId() && !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_event_index');
        }
        
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
            'activity_ids' => $userActivityArray,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $eventService->process($event);
            
            if(!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                $event->setUser($user);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Opération effectuée');
            return $this->redirectToRoute('admin_event_edit', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
        }


        return $this->render('admin/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager, EventService $eventService): Response
    {
        // delete images
        $eventService->deleteAllPictures($event); 

        // Edit data base 
        $entityManager->remove($event);
        $entityManager->flush();
        
        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pictureFileMain_delete', name: 'pictureFileMain_delete')]
    public function pictureFileMainDelete(Event $event, MediaService $mediaService, EntityManagerInterface $em)
    {
        // Delete data
        $mediaService->delete($event->getMainPicture());

        // Delete on base
        $event->setMainPicture(NULL);
        $em->flush();

        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_event_edit', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}/pictureFile2_delete', name: 'pictureFile2_delete')]
    public function pictureFile2Delete(Event $event, MediaService $mediaService, EntityManagerInterface $em)
    {
        // Delete data
        $mediaService->delete($event->getPicture2());

        // Delete on base
        $event->setPicture2(NULL);
        $em->flush();

        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_event_edit', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}/pictureFile3_delete', name: 'pictureFile3_delete')]
    public function pictureFile3Delete(Event $event, MediaService $mediaService, EntityManagerInterface $em)
    {
        // Delete data
        $mediaService->delete($event->getPicture3());

        // Delete on base
        $event->setPicture3(NULL);
        $em->flush();

        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_event_edit', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);

    }
}

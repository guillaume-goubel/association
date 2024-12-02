<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\EventService;
use App\Service\MediaService;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CheckAuthorizationService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/event', name: 'admin_event_')]
class EventController extends AbstractController
{
    
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
    
        $yearChoice = $request->query->get('yearChoice') ?? 'all';
        $monthChoice = $request->query->get('monthChoice') ?? 'all';
        $creatorChoice = $request->query->get('creatorChoice') ?? null;
        $animatorChoice = $request->query->get('animatorChoice') ?? 'all';
        $dateChoice = $request->query->get('dateChoice') ?? 'dateStartAt';
        $isPassedChoice = $request->query->get('isPassedChoice') ?? 'isNoPassed';
        $isCanceledChoice = $request->query->get('isCanceledChoice') ?? 'all';
        $activityChoice = $request->query->get('activityChoice') ?? "all";
        
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            if($creatorChoice == null){
                $creatorChoice = 'all';
            }
        }else{
            if($creatorChoice != 'all' && $creatorChoice == null){
                $creatorChoice = $this->getUser()->getId();
            }
        }
        
        // Distinct month / year createdAt for select
        $yearsList = $eventRepository->getDistincYearCreatedAt();
        $monthsList = $eventRepository->getDistinctMonthCreatedAt($yearChoice);
        $creatorsList = $eventRepository->getDistinctCreator();
        $animatorsList = $eventRepository->getDistinctAnimator();

        if ($creatorChoice == NULL) {
            if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                $activityList = $activityRepository->findBy([], ['name' => 'ASC']);
            }else{
                $activityList = $activityRepository->getActivitiesByUser($user->getId());
            }
        }else{
            $activityList = $activityRepository->getActivitiesByUser($creatorChoice);
        }
    
        // Pagination
        $eventsQuery = $eventRepository->getEventListforAdmin($yearChoice, $monthChoice, $creatorChoice, $activityChoice, $dateChoice, $isPassedChoice, $isCanceledChoice, $animatorChoice);
        
        // Définir la page actuelle (par défaut, la page 1)
        $page = $request->query->getInt('page', 1); 
    
        $pagination = $paginator->paginate(
            $eventsQuery, // la requête ou liste d'objets
            $page,        // page actuelle
            8   // nombre d'événements par page (vous pouvez ajuster ce chiffre)
        );
    
        return $this->render('admin/event/index.html.twig', [
            'events' => $pagination,  // utilisez la pagination ici
            'yearsList' => $yearsList,
            'monthsList' => $monthsList,
            'creatorsList' => $creatorsList,
            'animatorsList' => $animatorsList,
            'activityList' => $activityList,
            'yearChoice' => $yearChoice,
            'monthChoice' => $monthChoice,
            'creatorChoice' => $creatorChoice,
            'animatorChoice' => $animatorChoice,
            'activityChoice' => $activityChoice,
            'dateChoice' => $dateChoice,
            'isPassedChoice' => $isPassedChoice,
            'isCanceledChoice' => $isCanceledChoice,
            'isEventActionsButtonVisible' => true,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService): Response
    {
        $event = new Event();
        $user = $this->getUser();
        $userActivityArray = [];
        $activityChoice = null;
        $activityId = $request->query->get('activityId') ?? null;
        if ($activityId){
            $activityChoice = $activityRepository->findOneBy(['id' => $activityId]);
        }
        $creationDate = $request->query->get('creationDate') ?? null;

        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $users = $userRepository->adminsList();
            
            // Si le tableau n'est pas vide, on récupère le premier utilisateur
            if (count($users) > 0) {
                $userFirst = $users[0];
            } else {
                // Si le tableau est vide, $userFirst sera null
                $userFirst = null;
            }

            foreach ($activityRepository->findAll() as $activity) {
                if ($activity->isActivityInUserControl($userFirst) === true) {
                    array_push($userActivityArray, $activity->getId());
                }
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
            'selected_activity' => $activityChoice,
            'creation_date' => $creationDate,
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
    public function edit(Request $request, UserRepository $userRepository, Event $event, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService, CheckAuthorizationService $checkAuthorizationService): Response
    {   
        $user = $this->getUser();
        $isNewEvent = $event->getId() === null;
        
        if (!$checkAuthorizationService->checkProcess($user, 'admin_activity_edit', $event)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        $userActivityArray = [];

        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $users = $userRepository->adminsList();
            
            // Si le tableau n'est pas vide, on récupère le premier utilisateur
            if (count($users) > 0) {
                $userFirst = $users[0];
            } else {
                // Si le tableau est vide, $userFirst sera null
                $userFirst = null;
            }
                  
            foreach ($activityRepository->findAll() as $activity) {
                if ($activity->isActivityInUserControl($userFirst) === true) {
                    array_push($userActivityArray, $activity->getId());
                }
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
            'selected_activity' => $isNewEvent ? null : $event->getActivity()
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
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager, EventService $eventService, CheckAuthorizationService $checkAuthorizationService): Response
    {
        
        if (!$checkAuthorizationService->checkProcess($this->getUser(), 'admin_activity_delete', $event)) {
            // Redirige vers la liste des événements si les IDs ne correspondent pas
            return $this->redirectToRoute('admin_index');
        }
        
        // delete images
        $eventService->deleteAllEventPictures($event); 

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
        $mediaService->deleteEventPictures($event->getMainPicture());

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
        $mediaService->deleteEventPictures($event->getPicture2());

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
        $mediaService->deleteEventPictures($event->getPicture3());

        // Delete on base
        $event->setPicture3(NULL);
        $em->flush();

        $this->addFlash('success', 'Opération effectuée');
        return $this->redirectToRoute('admin_event_edit', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);

    }

    #[Route('/change/status', name: 'change_status', methods: ['POST'])]
    public function changeStatus(Request $request, EventRepository $eventRepository, EntityManagerInterface $em): JsonResponse
    {
        
        $eventIdNoformat = $request->request->get('eventId');
        $eventId = explode("-", $eventIdNoformat)[1];

        $event = $eventRepository->findOneBy(['id' => $eventId]);

        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], 404);
        }

        $newStatus = !$event->isEnabled();
        $event->setEnabled($newStatus);
        $em->persist($event);
        $em->flush();

        $eventData = [
            'id' => $event->getId(),
            'isEnabled' => $event->isEnabled()
        ];

        // Retourne la réponse JSON avec la liste des activités
        return new JsonResponse(['event' => $eventData]);
    }

    #[Route('/change/activities', name: 'change_activities', methods: ['POST'])]
    public function getActivitiesByUser(Request $request, ActivityRepository $activityRepository): JsonResponse
    {
        $userId = $request->request->get('userId');

        $activities = $activityRepository->getActivitiesByUser($userId);

        if (!$activities) {
            return new JsonResponse(['error' => 'Event not found'], 404);
        }

        $activitiesArray = [];
        foreach ($activities as $activity) {
            $activitiesArray [ ] = [
                'id'=> $activity->getId(),
                'name'=> $activity->getName(),
            ];
        }

        // Retourne la réponse JSON avec la liste des activités
        return new JsonResponse(['activitiesArray' => $activitiesArray]);
    }

}

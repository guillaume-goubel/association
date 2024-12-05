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
        // View type in session
        $session = $request->getSession();
        if ($session->get('event_view_type') === null) {
            $session->set('event_view_type', 'card');
        }
    
        $user = $this->getUser();
        
        // Obtenez les filtres depuis la requête ou la session
        $reload = $request->query->get('reload') ?? null;
        if ($reload) {
            $session->remove('filter_parameter');
            return $this->redirectToRoute('admin_event_index');
        }

        $filters = $this->getFilterParameters($request, $session);
    
        // Logique spécifique pour 'creatorChoice'
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            if ($filters['creatorChoice'] === null) {
                $filters['creatorChoice'] = 'all';
            }
        } else {
            if ($filters['creatorChoice'] === null) {
                $filters['creatorChoice'] = $user->getId();
            }
        }
    
        // Distinct month / year createdAt for select
        $yearsList = $eventRepository->getDistincYearCreatedAt();
        $monthsList = $eventRepository->getDistinctMonthCreatedAt($filters['yearChoice']);
        $creatorsList = $eventRepository->getDistinctCreator();
        $animatorsList = $eventRepository->getDistinctAnimator();
    
        if ($filters['creatorChoice'] === null) {
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                $activityList = $activityRepository->findBy([], ['name' => 'ASC']);
            } else {
                $activityList = $activityRepository->getActivitiesByUser($user->getId());
            }
        } else {
            $activityList = $activityRepository->getActivitiesByUser($filters['creatorChoice']);
        }
    
        // Pagination
        $eventsQuery = $eventRepository->getEventListforAdmin(
            $filters['yearChoice'],
            $filters['monthChoice'],
            $filters['creatorChoice'],
            $filters['activityChoice'],
            $filters['dateChoice'],
            $filters['isPassedChoice'],
            $filters['isCanceledChoice'],
            $filters['animatorChoice'],
            $filters['isEnabledChoice']
        );
    
        // Définir la page actuelle (par défaut, la page 1)
        $page = $request->query->getInt('page', 1);
    
        $limit = ($session->get('event_view_type') === 'card') ? 8 : 30;
        $pagination = $paginator->paginate(
            $eventsQuery,
            $page,
            $limit
        );
    
        return $this->render('admin/event/index.html.twig', [
            'events' => $pagination,
            'yearsList' => $yearsList,
            'monthsList' => $monthsList,
            'creatorsList' => $creatorsList,
            'animatorsList' => $animatorsList,
            'activityList' => $activityList,
            'yearChoice' => $filters['yearChoice'],
            'monthChoice' => $filters['monthChoice'],
            'creatorChoice' => $filters['creatorChoice'],
            'animatorChoice' => $filters['animatorChoice'],
            'activityChoice' => $filters['activityChoice'],
            'dateChoice' => $filters['dateChoice'],
            'isPassedChoice' => $filters['isPassedChoice'],
            'isCanceledChoice' => $filters['isCanceledChoice'],
            'isEnabledChoice' => $filters['isEnabledChoice'],
            'isEventActionsButtonVisible' => true,
            'viewType' => $session->get('event_view_type') ?? 'card',
        ]);
    }
    
    private function getFilterParameters(Request $request, $session): array
    {
        $defaults = [
            'yearChoice' => 'all',
            'monthChoice' => 'all',
            'creatorChoice' => null,
            'animatorChoice' => 'all',
            'dateChoice' => 'dateStartAt',
            'isPassedChoice' => 'isNoPassed',
            'isCanceledChoice' => 'all',
            'isEnabledChoice' => 'all',
            'activityChoice' => 'all',
        ];

        // Récupérer les filtres depuis la requête ou la session
        $filters = [];
        foreach ($defaults as $key => $default) {
            $filters[$key] = $request->query->get($key, $session->get("filter_parameter")[$key] ?? $default);
        }

        // Mettre à jour la session avec les nouveaux filtres
        $session->set('filter_parameter', $filters);

        return $filters;
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService): Response
    {
        $event = new Event();
        $user = $this->getUser();
        $userActivityArray = [];
        $activityChoice = $activityByDefault = null;
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

        if (!empty($userActivityArray)) {
            $activityDefaultListId = $userActivityArray[0];
            $activityByDefault = $activityRepository->findOneBy(['id' => $activityDefaultListId]);
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
            'activityChoice' => $activityChoice,
            'activityByDefault' => $activityByDefault,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository, Event $event, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, EventService $eventService, CheckAuthorizationService $checkAuthorizationService): Response
    {   
        $user = $this->getUser();
        $isNewEvent = $event->getId() === null;
        
        if (!$checkAuthorizationService->checkProcess($user, 'admin_activity_edit', $event)) {
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
            'selected_activity' => $isNewEvent ? null : $event->getActivity(),
            'is_passed' => $event->isPassed()
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

    #[Route('/change/session/viewtype', name: 'change_session_view_type', methods: ['POST'])]
    public function changeViewTypeSession(Request $request): JsonResponse
    {
        $session = $request->getSession();
        
        if ($session->get('event_view_type') == 'card') {
            $session->set('event_view_type', 'list');
        }else{
            $session->set('event_view_type', 'card');
        }
        
        return new JsonResponse();
    }

}

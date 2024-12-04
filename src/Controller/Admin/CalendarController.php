<?php

namespace App\Controller\Admin;

use App\Repository\ActivityRepository;
use App\Repository\EventRepository;
use App\Service\EventService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/calendar', name: 'admin_calendar_')]
class CalendarController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository, EventService $eventService): Response
    {
        // Obtenez les filtres depuis la requête ou la session
        $session = $request->getSession();
        $filters = $this->getFilterParameters($request, $session);
    
        $user = $this->getUser();

        // Obtenez les filtres depuis la requête ou la session
        $reload = $request->query->get('reload') ?? null;
        if ($reload) {
            $session->remove('filter_parameter_calendar');
            return $this->redirectToRoute('admin_calendar_index');
        }
    
        // Logique spécifique pour 'creatorChoice'
        // if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
        //     if ($filters['creatorChoice'] === null) {
        //         $filters['creatorChoice'] = 'all';
        //     }
        // } else {
        //     if ($filters['creatorChoice'] === null) {
        //         $filters['creatorChoice'] = $user->getId();
        //     }
        // }
    
        // Récupérer les listes basées sur les filtres
        $activityList = $activityRepository->findByMonthAndYearForCalendar($filters['yearChoice']);
        $creatorList = $eventRepository->getDistinctCreator();
        $animatorsList = $eventRepository->getDistinctAnimator();
    
        $events = $eventRepository->getEventListforCalendarFor12Months(
            $filters['activityChoice'],
            $filters['creatorChoice'],
            $filters['yearChoice'],
            $filters['animatorChoice'],
            $filters['isPassedChoice'],
            $filters['isCanceledChoice'],
            $filters['isEnabledChoice']
        );
    
        $eventDateJson = $eventService->getEventListForCalendarEvents($events);
    
        return $this->render('admin/calendar/index.html.twig', [
            'eventsCount' => count($events),
            'eventDateJson' => $eventDateJson,
            'activityList' => $activityList,
            'activityChoice' => $filters['activityChoice'],
            'yearChoice' => $filters['yearChoice'],
            'yearsList' => $eventRepository->getDistincYearCreatedAt(),
            'creatorList' => $creatorList,
            'creatorChoice' => $filters['creatorChoice'],
            'animatorChoice' => $filters['animatorChoice'],
            'animatorsList' => $animatorsList,
            'isPassedChoice' => $filters['isPassedChoice'],
            'isCanceledChoice' => $filters['isCanceledChoice'],
            'isEnabledChoice' => $filters['isEnabledChoice'],
        ]);
    }

    private function getFilterParameters(Request $request, $session): array
{
    $defaults = [
        'yearChoice' => 'yearDepth',
        'activityChoice' => 'all',
        'creatorChoice' => 'all',
        'animatorChoice' => 'all',
        'isPassedChoice' => 'isNoPassed',
        'isCanceledChoice' => 'all',
        'isEnabledChoice' => 'all',
    ];

    // Récupérer les filtres depuis la requête ou la session
    $filters = [];
    foreach ($defaults as $key => $default) {
        $filters[$key] = $request->query->get($key, $session->get("filter_parameter_calendar")[$key] ?? $default);
    }

    // Mettre à jour la session avec les nouveaux filtres
    $session->set('filter_parameter_calendar', $filters);

    return $filters;
}
}
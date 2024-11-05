<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
    public function index(ActivityRepository $activityRepository, EventRepository $eventRepository): Response
    {

        return $this->render('home/index.html.twig', [
            "activities" => $activityRepository->findBy(["isEnabled"=>1], ['ordering' => 'ASC']),
            'events_regular' => $eventRepository->findEventsByRegularActivity(),
            'events_journey' => $eventRepository->findEventsByJourneyActivity()
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\ActivityRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route('/{id}/index', name: 'index')]
    public function index(Event $event, Request $request, EventRepository $eventRepository, ActivityRepository $activityRepository): Response
    {   
        return $this->render('blog/post_page/index.html.twig', [
            'is_passed' => $request->query->get('is_passed') ?? $event->isPassed(),
            'event' => $event,
            'lastEventCreated' => $eventRepository->findLastEventsforUser(3, $event->getId()),
            'getActivitiesByEvents' => $activityRepository->getActivitiesByEvents($event->getActivity()->getId()),
        ]);
    }
}

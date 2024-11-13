<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/calendar', name: 'calendar_')]
class CalendarController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {   
        $yearChoice = $request->query->get('yearChoice') ?? date("Y");
        $activityChoice = $request->query->get('activityChoice') ?? "all";

        return $this->render('calendar/index.html.twig', [

        ]);
    }
}
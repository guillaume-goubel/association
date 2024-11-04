<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
    public function index(ActivityRepository $activityRepository): Response
    {
        $activities = $activityRepository->findBy(["isEnabled"=>1], ['ordering' => 'ASC']);

        return $this->render('home/index.html.twig', [
            "activities" => $activities,
        ]);
    }

}

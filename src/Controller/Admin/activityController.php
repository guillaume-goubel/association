<?php

namespace App\Controller\Admin;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]

class activityController extends AbstractController
{
    #[Route('/activity', name: 'activity')]
    public function activities(ActivityRepository $activityRepository): Response
    {   
        $activities =$activityRepository->findBy(["isEnabled"=>1], ['ordering' => 'ASC']);

        return $this->render('admin/activity/activity.html.twig', [
            "activities" => $activities,
        ]);
    }
}

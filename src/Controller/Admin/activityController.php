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
    public function index(ActivityRepository $activityRepository): Response
    {   
        $user = $this->getUser();

        $activities = $activityRepository->findBy(["isEnabled"=>1], ['ordering' => 'ASC']);

        $userActivityArray = [];
        $noUserActivityArray = [];
        foreach ($activities as $activity) {
            if ($activity->isActivityInUserControl($user) === true) {
                array_push($userActivityArray, $activity);
            }else{
                array_push($noUserActivityArray, $activity);
            }
        }

        return $this->render('admin/activity/index.html.twig', [
            "activities" => $activities,
            "userActivityArray" => $userActivityArray,
            "noUserActivityArray" => $noUserActivityArray
        ]);

    }
}

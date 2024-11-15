<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/activity', name: 'activity_')]
class ActivityController extends AbstractController
{
    #[Route('/dynamic/agenda', name: 'dynamic_agenda', methods: ['POST'])]
    public function getFilteredActivitiesAgenda(Request $request, ActivityRepository $activityRepository): JsonResponse
    {
        $month = $request->request->get('month');
        $year = $request->request->get('year');
        
        $activities = $activityRepository->findByMonthAndYearForAgenda($month, $year);
        
        $activityData = [];
        foreach ($activities as $activity) {
            $activityData[] = [
                'id' => $activity->getId(),
                'name' => $activity->getName()
            ];
        }
    
        // Retourne la réponse JSON avec la liste des activités
        return new JsonResponse(['activities' => $activityData]);
    }

    #[Route('/dynamic/archive', name: 'dynamic_archive', methods: ['POST'])]
    public function getFilteredActivitiesArchive(Request $request, ActivityRepository $activityRepository): JsonResponse
    {
        $month = $request->request->get('month');
        $year = $request->request->get('year');
        
        $activities = $activityRepository->findByMonthAndYearForArchive($month, $year);

        $activityData = [];
        foreach ($activities as $activity) {
            $activityData[] = [
                'id' => $activity->getId(),
                'name' => $activity->getName()
            ];
        }
    
        // Retourne la réponse JSON avec la liste des activités
        return new JsonResponse(['activities' => $activityData]);
    }

}

<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]

class EventController extends AbstractController
{
    #[Route('/admin/event', name: 'app_admin_event')]
    public function index(): Response
    {   
        

        return $this->render('admin/event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }
}

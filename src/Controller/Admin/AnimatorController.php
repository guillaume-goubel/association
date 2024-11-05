<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]

class AnimatorController extends AbstractController
{
    #[Route('/animator', name: 'animator')]
    public function index(): Response
    {
        return $this->render('admin/animator/index.html.twig', [
            'controller_name' => 'AnimatorController',
        ]);
    }
}

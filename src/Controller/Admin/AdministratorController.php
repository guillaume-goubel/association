<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]

class AdministratorController extends AbstractController
{
    #[Route('/administrator', name: 'administrator')]
    public function index(): Response
    {
        return $this->render('admin/administrator/index.html.twig', [
            'controller_name' => 'administratorController',
        ]);
    }
}

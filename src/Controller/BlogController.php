<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('blog/post_page/index.html.twig', [
            'is_passed' => true,
        ]);
    }

    #[Route('/index2', name: 'index2')]
    public function index2(): Response
    {
        return $this->render('blog/post_page/index.html.twig', [
            'is_passed' => false,
        ]);
    }
}

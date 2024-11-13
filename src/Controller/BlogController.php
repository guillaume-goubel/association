<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route('/{id}/index', name: 'index')]
    public function index(Event $event, Request $request): Response
    {   
        return $this->render('blog/post_page/index.html.twig', [
            'is_passed' => $request->query->get('is_passed') ?? null,
            'event' => $event
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

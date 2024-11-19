<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;


class CheckUserAccessSubscriber implements EventSubscriberInterface
{
    private Security $security;


    // Injection des services Security et FlashBag
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }
        
        // Récupérer l'utilisateur connecté via le service Security
        $user = $this->security->getUser();

        // Récupérer la route actuelle
        $request = $event->getRequest();
        $route = $request->get('_route');

        // Vérifier si la route commence par 'admin_' et si l'utilisateur est désactivé
        if ($user && !$user->getIsEnabled() && strpos($route, 'admin_') === 0) {
            // Ajouter un flash message
            $request->getSession()->getFlashBag()->add('error', "Votre compte est désactivé. Veuillez contacter l'administrateur.");

            // Rediriger vers la page de login (ajustez l'URL si nécessaire)
            $event->setResponse(new RedirectResponse('/login'));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',  // S'abonner à l'événement de la requête
        ];
    }
}

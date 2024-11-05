<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface 
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    // public function authenticate(Request $request): Passport
    // {
        
    //     $email = $request->request->get('email', '');

    //     $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

    //     return new Passport(
    //         new UserBadge($email),
    //         new PasswordCredentials($request->request->get('password', '')),
    //         [
    //             new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
    //             new RememberMeBadge()
    //         ]
    //     );
    // }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return new RedirectResponse($this->router->generate('admin_index'));
        }

        return new RedirectResponse($this->router->generate('home_index'));
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $request->getPathInfo() === '/login';
    }
}

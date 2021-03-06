<?php
namespace AppBundle\EventHandler;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $redirect = $request->headers->get('referer') ?? $this->router->generate('home');
        return new RedirectResponse($redirect);
    }
}

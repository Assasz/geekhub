<?php
namespace AppBundle\EventHandler;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\Router;

class LoginHandler implements AuthenticationSuccessHandlerInterface
{
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $router = new Router();
        $redirect = $request->headers->get('referer') ?? $router->generate('home');
        return new RedirectResponse($redirect);
    }
}

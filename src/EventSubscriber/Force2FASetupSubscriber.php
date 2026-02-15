<?php

namespace App\EventSubscriber;

use App\Entity\Employee;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Force les utilisateurs à configurer la 2FA au premier login
 * Si l'utilisateur est authentifié mais n'a pas activé la 2FA, 
 * il est redirigé vers /2fa/setup
 */
class Force2FASetupSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priorité après le firewall (qui est à 8) pour avoir accès au token
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Ne pas rediriger si on est déjà sur les pages 2FA ou logout
        $excludedRoutes = [
            'app_2fa_setup', 
            'app_2fa_enable', 
            '2fa_login', 
            '2fa_login_check', 
            'app_logout',
            'app_login'
        ];
        
        if (in_array($route, $excludedRoutes)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        
        if (!$token) {
            return;
        }

        $user = $token->getUser();

        // Si l'utilisateur est un Employee authentifié
        if ($user instanceof Employee) {
            // Mode bypass en développement
            if (isset($_ENV['BYPASS_2FA']) && $_ENV['BYPASS_2FA'] === '1') {
                return;
            }

            // Si la 2FA n'est pas activée, forcer la configuration
            if (!$user->isGoogleAuthenticatorEnabled()) {
                $setupUrl = $this->urlGenerator->generate('app_2fa_setup');
                $event->setResponse(new RedirectResponse($setupUrl));
            }
        }
    }
}

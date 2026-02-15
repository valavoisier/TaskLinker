<?php

namespace App\EventListener;

use App\Entity\Employee;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Intercepte le succès de l'authentification pour forcer la configuration 2FA
 */
#[AsEventListener(event: LoginSuccessEvent::class, priority: -10)]
class LoginSuccessListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        // Si l'utilisateur est un Employee
        if (!$user instanceof Employee) {
            return;
        }

        // Mode bypass en développement
        if (isset($_ENV['BYPASS_2FA']) && $_ENV['BYPASS_2FA'] === '1') {
            return;
        }

        // Si la 2FA n'est pas activée, forcer la configuration
        if (!$user->isGoogleAuthenticatorEnabled()) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_2fa_setup'));
            $event->setResponse($response);
        }
    }
}

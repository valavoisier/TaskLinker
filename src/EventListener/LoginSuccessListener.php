<?php

namespace App\EventListener;

use App\Entity\Employee;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Intercepte le succès de l'authentification (bypass en développement uniquement)
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

        // La 2FA est maintenant optionnelle, la popup s'affichera si nécessaire
        // Pas de redirection forcée
    }
}

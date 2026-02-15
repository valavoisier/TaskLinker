<?php

namespace App\Security;

use App\Entity\Employee;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Redirige vers la configuration 2FA si l'utilisateur n'a pas encore activé la 2FA
 */
class TwoFactorAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private string $defaultTargetPath = '/project'
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        // Si l'utilisateur est un Employee
        if ($user instanceof Employee) {
            // Mode bypass en développement
            if (isset($_ENV['BYPASS_2FA']) && $_ENV['BYPASS_2FA'] === '1') {
                return new RedirectResponse($this->urlGenerator->generate('project_index'));
            }

            // Si la 2FA n'est pas activée, rediriger vers la configuration
            if (!$user->isGoogleAuthenticatorEnabled()) {
                return new RedirectResponse($this->urlGenerator->generate('app_2fa_setup'));
            }
        }

        // Sinon, redirection par défaut
        return new RedirectResponse($this->urlGenerator->generate('project_index'));
    }
}

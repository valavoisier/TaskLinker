<?php

namespace App\Security;

use App\Entity\Employee;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Gère la redirection après l'authentification (avec ou sans 2FA)
 * La popup 2FA s'affichera automatiquement si nécessaire (approche optionnelle)
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

        // Si l'utilisateur est un Employee en mode bypass développement
        if ($user instanceof Employee && isset($_ENV['BYPASS_2FA']) && $_ENV['BYPASS_2FA'] === '1') {
            return new RedirectResponse($this->urlGenerator->generate('project_index'));
        }

        // Redirection par défaut (la popup 2FA s'affichera si nécessaire)
        return new RedirectResponse($this->urlGenerator->generate('project_index'));
    }
}

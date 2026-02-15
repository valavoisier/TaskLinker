<?php

namespace App\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TwoFactorController extends AbstractController
{
    #[Route('/2fa/setup', name: 'app_2fa_setup')]
    public function setup(
        GoogleAuthenticatorInterface $googleAuthenticator,
        EntityManagerInterface $em
    ): Response {
        /** @var Employee $user */
        $user = $this->getUser();

        if (!$user instanceof Employee) {
            throw $this->createAccessDeniedException();
        }

        // Si l'utilisateur n'a pas encore de secret, on en génère un
        if (!$user->getGoogleAuthenticatorSecret()) {
            $secret = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $em->flush();
        }

        // Contenu du QR Code (URI otpauth://…)
        $qrCodeContent = $googleAuthenticator->getQRContent($user);

        return $this->render('security/2fa_setup.html.twig', [
            'qrCodeContent' => $qrCodeContent,
            'secret' => $user->getGoogleAuthenticatorSecret(),
            'active_menu' => '2fa',
        ]);
    }

    #[Route('/2fa/enable', name: 'app_2fa_enable')]
    public function enable(Request $request, GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $em): Response
    {
        /** @var Employee $user */
        $user = $this->getUser();

        if (!$user instanceof Employee) {
            throw $this->createAccessDeniedException();
        }

        // Si la requête est POST, on récupère le code et on le vérifie
        $error = null;
        if ($request->isMethod('POST')) {
            $code = (string) $request->request->get('_auth_code', '');
            if ($googleAuthenticator->checkCode($user, $code)) {
                $user->setIsTwoFactorEnabled(true);
                $em->flush();

                $this->addFlash('success', 'La double authentification est maintenant activée.');
                return $this->redirectToRoute('project_index');
            }

            $error = 'Code invalide. Veuillez réessayer.';
        }

        return $this->render('security/2fa_form.html.twig', [
            'authenticationError' => $error,
            'checkPathUrl' => $this->generateUrl('app_2fa_enable'),
        ]);
    }

    #[Route('/2fa/hide-prompt', name: 'app_2fa_hide_prompt', methods: ['POST'])]
    public function hidePrompt(EntityManagerInterface $em): Response
    {
        /** @var Employee $user */
        $sessionUser = $this->getUser();

        if (!$sessionUser instanceof Employee) {
            return $this->json(['success' => false, 'error' => 'User not authenticated']);
        }

        // Recharger l'utilisateur depuis la base pour s'assurer qu'il est géré par Doctrine
        $user = $em->getRepository(Employee::class)->find($sessionUser->getId());
        
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'User not found in database']);
        }

        $before = $user->getHide2FAPrompt();
        $user->setHide2FAPrompt(true);
        $after = $user->getHide2FAPrompt();
        
        // Forcer Doctrine à recalculer les changements
        $em->persist($user);
        $em->flush();
        
        return $this->json([
            'success' => true,
            'before' => $before,
            'after' => $after,
            'userId' => $user->getId(),
            'debug' => 'User reloaded from DB and updated'
        ]);
    }
}

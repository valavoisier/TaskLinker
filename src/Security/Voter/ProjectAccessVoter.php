<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class ProjectAccessVoter extends Voter
{
    public const EDIT = 'PROJECT_EDIT';
    public const VIEW = 'PROJECT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof Employee) {
            return false;
        }

        // 1) Les admins (chefs de projet) ont accès à tout 
        if (\in_array('ROLE_ADMIN', $user->getRoles(), true)) { 
            return true; 
        }

        /** @var Project $project */
        $project = $subject;

        // 2) Vérifier que l'utilisateur est membre du projet
        if (!$project->getEmployees()->contains($user)) {
            return false;
        }

        // 3) Selon l'action demandée
        return match ($attribute) {
            self::VIEW => true, // Tous les membres assignés peuvent voir le projet
            self::EDIT => false, // Seul ROLE_ADMIN peut modifier (déjà vérifié en 1)
            default => false,
        };
    }
}

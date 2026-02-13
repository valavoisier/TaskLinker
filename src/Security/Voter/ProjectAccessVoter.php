<?php

namespace App\Security\Voter;

use App\Entity\Project; 
use App\Entity\Employee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjectAccessVoter extends Voter
{
    public const EDIT = 'PROJECT_EDIT';
    public const VIEW = 'PROJECT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof Employee) {
            return false;
        }

        // 1) Les admins ont accès à tout 
        if (\in_array('ROLE_ADMIN', $user->getRoles(), true)) { return true; } 
        // 2) Collaborateurs : accès uniquement si assignés au projet 
        return $subject->getEmployees()->contains($user);
    }
}

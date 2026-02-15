<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TaskVoter extends Voter
{
    public const VIEW = 'TASK_VIEW';
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';
    public const CHANGE_STATUS = 'TASK_CHANGE_STATUS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::CHANGE_STATUS])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas authentifié, refuser l'accès
        if (!$user instanceof Employee) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;
        $project = $task->getProject();

        // 1) Les admins ont accès à tout
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // 2) Vérifier que l'utilisateur est membre du projet
        if (!$project->getEmployees()->contains($user)) {
            return false;
        }

        // 3) Vérifier les permissions selon le rôle et l'action
        return match ($attribute) {
            self::VIEW => $this->canView($user, $task, $project),
            self::EDIT => $this->canEdit($user, $task, $project),
            self::DELETE => $this->canDelete($user, $task, $project),
            self::CHANGE_STATUS => $this->canChangeStatus($user, $task, $project),
            default => false,
        };
    }

    /**
     * Tous les membres du projet peuvent voir les tâches
     */
    private function canView(Employee $user, Task $task, $project): bool
    {
        // Déjà vérifié que l'utilisateur est membre du projet
        return true;
    }

    /**
     * - ROLE_ADMIN : peut modifier toutes les tâches du projet
     * - ROLE_USER : peut modifier uniquement ses propres tâches
     */
    private function canEdit(Employee $user, Task $task, $project): bool
    {
        // ROLE_ADMIN peut tout modifier
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // ROLE_USER peut modifier uniquement ses propres tâches
        return $task->getEmployee() === $user;
    }

    /**
     * - ROLE_ADMIN : peut supprimer toutes les tâches du projet
     * - ROLE_USER : NE peut PAS supprimer de tâches
     */
    private function canDelete(Employee $user, Task $task, $project): bool
    {
        // Seul l'admin (chef de projet) peut supprimer des tâches
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    /**
     * - ROLE_ADMIN : peut changer le statut de toutes les tâches
     * - ROLE_USER : peut changer le statut uniquement de ses propres tâches
     */
    private function canChangeStatus(Employee $user, Task $task, $project): bool
    {
        // Admin peut tout faire
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // ROLE_USER peut changer le statut uniquement de ses propres tâches
        if (in_array('ROLE_USER', $user->getRoles(), true)) {
            return $task->getEmployee() === $user;
        }

        return false;
    }
}

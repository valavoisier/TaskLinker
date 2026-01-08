<?php

namespace App\Enum;
/**
 * Enum représente le statut d'une tâche.
 */
enum TaskStatus: string
{
    case TODO = 'todo';
    case DOING = 'doing';
    case DONE = 'done';

    // Retourne le label lisible du statut.
    public function getLabel(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::DOING => 'Doing',
            self::DONE => 'Done',
        };
    }

    // Retourne un tableau des choix disponibles pour les statuts de tâche.
    public static function choices(): array
    {
        return [
            self::TODO->getLabel() => self::TODO,
            self::DOING->getLabel() => self::DOING,
            self::DONE->getLabel() => self::DONE,
        ];
    }
}


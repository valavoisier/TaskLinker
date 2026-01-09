<?php

namespace App\Enum;

/**
 * Enum représente le statut d'un employé.
 */
enum EmployeeStatus: string
{
    case CDI = 'cdi';
    case CDD = 'cdd';
    case FREELANCE = 'freelance';

    /* Retourne le label lisible du statut.*/
    public function getLabel(): string
    {
        return match ($this) {
            self::CDI => 'CDI',
            self::CDD => 'CDD',
            self::FREELANCE => 'Freelance',
        };
    }

    /* Retourne un tableau des choix disponibles pour les statuts d'employé.*/
    public static function choices(): array
    {
        return [
            self::CDI->getLabel() => self::CDI, 
            self::CDD->getLabel() => self::CDD, 
            self::FREELANCE->getLabel() => self::FREELANCE,
        ];
    }
}

<?php

namespace App\Factory;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Employee>
 */
final class EmployeeFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    public static function class(): string
    {
        return Employee::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastName(),
            'email' => self::faker()->unique()->safeEmail(),
            'entryDate' => self::faker()->dateTimeBetween('-5 years', 'now'),
            'status' => self::faker()->randomElement(EmployeeStatus::cases()),
            'password' => 'hashed_password', // Mot de passe par défaut (sera haché si nécessaire)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(Employee $employee): void {
                // Définir un rôle par défaut si aucun rôle n'est défini
                // 20% de chance d'être admin, 80% d'être user
                $isAdmin = self::faker()->boolean(20);
                $employee->setMainRole($isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER');
            })
        ;
    }
}

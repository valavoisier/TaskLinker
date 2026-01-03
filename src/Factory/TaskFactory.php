<?php

namespace App\Factory;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    public static function class(): string
    {
        return Task::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(4), 
            'description' => self::faker()->paragraph(), 
            'deadline' => self::faker()->optional()->dateTimeBetween('now', '+6 months'),
            'status' => self::faker()->randomElement(TaskStatus::cases()),// Statut aléatoire parmi les valeurs de l'énumération TaskStatus
            'project' => ProjectFactory::random(),// Associer chaque tâche à un projet aléatoire
            'employee' => self::faker()->boolean(70) ? EmployeeFactory::random() : null,// 70% de chances d'assigner un employé
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}

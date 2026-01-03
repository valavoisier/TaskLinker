<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\ProjectFactory;
use App\Factory\EmployeeFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        EmployeeFactory::createMany(20);
        ProjectFactory::createMany(10);
        TaskFactory::createMany(50);

        $manager->flush();
    }
}

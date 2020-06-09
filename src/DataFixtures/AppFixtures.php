<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $figure = new Figure();
            $faker = Faker\Factory::create('fr_FR');
            $figure->setTitle($faker->sentence(2, true));
            $figure->setUserId(mt_rand(1, 50));
            $figure->setDescription($faker->paragraph());
            $figure->setCreatedAt(
                $faker->dateTimeThisMonth('now', 'Europe/Paris')
            );
            $manager->persist($figure);
        }

        $manager->flush();
    }
}

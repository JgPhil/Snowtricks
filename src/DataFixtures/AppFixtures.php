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
        $faker = Faker\Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();
        $usernames = array_column($users, 'username');

        for ($j = 0; $j < 10; $j++) {
            $figure = new Figure();

            $figure->setTitle($faker->sentence(2, true));
            $figure->setAuthor(extract($usernames[array_rand($usernames)]));
            $figure->setDescription($faker->paragraph());
            $figure->setCreatedAt(
                $faker->dateTimeThisMonth('now', 'Europe/Paris')
            );
            $manager->persist($figure);
        }

        $manager->flush();
    }
}

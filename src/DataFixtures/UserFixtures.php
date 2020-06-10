<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $faker = Faker\Factory::create('fr_FR');
            $user->setUsername($faker->firstName());
            $user->setPassword(
                password_hash($faker->password(), PASSWORD_BCRYPT)
            );
            $user->setEmail($faker->email())
                ->setCreatedAt(
                    $faker->dateTimeThisMonth('now', 'Europe/Paris')
                );
            $manager->persist($user);
        }
        $manager->flush();
    }
}

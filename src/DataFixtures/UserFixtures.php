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
        $faker = Faker\Factory::create('fr_FR');
        //admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword(password_hash('admin', PASSWORD_BCRYPT));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin
            ->setEmail($faker->email())
            ->setCreatedAt($faker->dateTimeThisMonth('now', 'Europe/Paris'));
        $manager->persist($admin);

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername($faker->firstName());
            $user->setPassword(
                password_hash($faker->password(), PASSWORD_BCRYPT)
            );
            $user->setRoles(['ROLE_USER']);
            $user
                ->setEmail($faker->email())
                ->setCreatedAt(
                    $faker->dateTimeThisMonth('now', 'Europe/Paris')
                )
                ;
            $manager->persist($user);
        }
        $manager->flush();
    }
}

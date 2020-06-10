<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();


        for ($i = 0; $i < 6; $i++) {

            $category = new Category();
            $category->setTitle($faker->word())
                ->setDescription($faker->sentence(16));
            $manager->persist($category);

            for ($j = 0; $j < 10; $j++) {
                $figure = new Figure();
                $figure->setTitle($faker->sentence(2))
                    ->setAuthor($users[array_rand($users, 1)])
                    ->setDescription($faker->paragraph())
                    ->setCreatedAt(
                        $faker->dateTimeThisMonth('now', 'Europe/Paris')
                    );
                $manager->persist($figure);

                for ($k = 0; $k < 12; $k++) {
                    $comment = new Comment;
                    $now = new \DateTime();
                    $days = $now->diff($figure->getCreatedAt())->days;

                    $comment->setContent($faker->sentence(12))
                        ->setAuthor($users[array_rand($users, 1)])
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                        ->setFigure($figure);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}

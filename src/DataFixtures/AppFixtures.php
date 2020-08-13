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
            $category
                ->setTitle($faker->word());
            $manager->persist($category);

            for ($j = 0; $j < mt_rand(2, 5); $j++) {
                $figure = new Figure();
                $figure
                    ->setTitle($faker->sentence(2))
                    ->setAuthor($users[array_rand($users, 1)])
                    ->setDescription($faker->paragraph())
                    ->setCategory($category)
                    ->setCreatedAt(
                        $faker->dateTimeThisMonth('now', 'Europe/Paris')
                    );
                $manager->persist($figure);

                for ($k = 0; $k < mt_rand(20, 40); $k++) {
                    $comment = new Comment();
                    $now = new \DateTime();
                    $days = $now->diff($figure->getCreatedAt())->days;

                    $comment
                        ->setContent($faker->sentence(12))
                        ->setAuthor($users[array_rand($users, 1)])
                        ->setCreatedAt(
                            $faker->dateTimeBetween('-' . $days . ' days')
                        )
                        ->setFigure($figure);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

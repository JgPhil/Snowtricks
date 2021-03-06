<?php

namespace App\DataFixtures;

use Faker;
use DateTime;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Category;
use App\Helpers\Slugify;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    /* public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 6; $i++) {
            $category = new Category();
            $category->setTitle($faker->word());
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
    } */

    public function getDependencies()
    {
        return [UserFixtures::class];
    }

    /**
     * Fixture method with a data array -- please rename the method into "load"
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $categoryArray = [
            [
                'title' => 'Flips',
                'figures' => [
                    [
                        'title' => 'Backflip',
                        'description' =>
                            'Le backflip figure parmi les sauts les plus spectaculaires de cette discipline. Il nécessite la maîtrise des fondamentaux et d’une bonne perception du corps. En effet, avoir la tête en bas, même pendant quelques secondes seulement, est très difficile pour les non-initiés.',
                        'picture' =>
                            'Backflip.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/W853WVF5AqI',
                    ],
                    [
                        'title' => 'Mc Twist',
                        'description' =>
                            "Le Mc Twist est un flip (rotation verticale) agrémenté d'une vrille. Un saut plutôt périlleux réservé aux riders les plus confirmés. Le champion Shaun White s'est illustré par un Double Mc Twist 1260 lors de sa session de Half-Pipe aux Jeux Olympiques de Vancouver en 2010.",
                        'picture' =>
                            'McTwist.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/k-CoAquRSwY',
                    ],
                    [
                        'title' => 'Wildcat',
                        'description' =>
                            'Aussi appelé backflip, le wildcat est un salto arrière que le rider effectue dans les airs après avoir pris de la vitesse. C\'est un trick qui peut être difficile à réaliser puisque le snowboardeur doit veiller à rester dans le bon axe.',
                        'picture' =>
                            'Wildcat.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/7KUpodSrZqI',
                    ],
                ],
            ],

            [
                'title' => 'Grabs',
                'figures' => [
                    [
                        'title' => 'Double Backflip One Foot',
                        'description' =>
                            "Comme on peut le deviner, les \"one foot\" sont des figures réalisées avec un pied décroché de la fixation. Pendant le saut, le snowboarder doit tendre la jambe du côté dudit pied. L'esthète Scotty Vine – une sorte de Danny MacAskill du snowboard – en a réalisé un bel exemple avec son Double Backflip One Foot.",
                        'picture' =>
                            'DoubleBackflipOneFoot.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/ty5o0JMEBAQ',
                    ],
                    [
                        'title' => 'Method air',
                        'description' =>
                            "Cette figure – qui consiste à attraper sa planche d'une main et le tourner perpendiculairement au sol – est un classique \"old school\". Il n'empêche qu'il est indémodable, avec de vrais ambassadeurs comme Jamie Lynn ou la star Terje Haakonsen. En 2007, ce dernier a même battu le record du monde du \"air\" le plus haut en s'élevant à 9,8 mètres au-dessus du kick (sommet d'un mur d'une rampe ou autre structure de saut).",
                        'picture' =>
                            'MethodAir.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/ZWZGE9yp5hA',
                    ],
                    [
                        'title' => 'Backside Triple Cork 1440',
                        'description' =>
                            " Comme on peut le deviner, les \"one foot\" sont des figures réalisées avec un pied décroché de la fixation. Pendant le saut, le snowboarder doit tendre la jambe du côté dudit pied. L'esthète Scotty Vine – une sorte de Danny MacAskill du snowboard – en a réalisé un bel exemple avec son Double Backflip One Foot.",
                        'picture' =>
                            'BacksideTripleCork1440.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/URFnYGzu9lU',
                    ],
                ],
            ],

            [
                'title' => 'Spins',
                'figures' => [
                    [
                        'title' => 'Cork',
                        'description' =>
                            "Le diminutif de corkscrew qui signifie littéralement tire-bouchon et désignait les premières simples rotations têtes en bas en frontside. Désormais, on utilise le mot cork à toute les sauces pour qualifier les figures où le rider passe la tête en bas, peu importe le sens de rotation. Et dorénavant en compétition, on parle souvent de double cork, triple cork et certains riders vont jusqu'au quadruple cork !",
                        'picture' =>
                            'Cork.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/FMHiSF0rHF8',
                    ],
                    [
                        'title' => 'Double Backside Rodeo 1080',
                        'description' =>
                            "À l'instar du cork, le rodeo est une rotation désaxée, qui se reconnaît par son aspect vrillé. Un des plus beaux de l'histoire est sans aucun doute le Double Backside Rodeo 1080 effectué pour la première fois en compétition par le jeune prodige Travis Rice, lors du Icer Air 2007. La pirouette est tellement culte qu'elle a fini dans un jeu vidéo où Travis Rice est l'un des personnages.",
                        'picture' =>
                            'DoubleBacksideRodeo1080.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/DSc7hQ0bzg',
                    ],
                ],
            ],

            [
                'title' => 'Slides',
                'figures' => [
                    [
                        'title' => 'Jib',
                        'description' => "Mais qu’est-ce que le Jib ? C’est le fait de glisser sur des obstacles tels que des rails ou des box avec un snowboard. Il y a de nombreuses façons de faire du jib en snow. C’est aussi ce qui rend le concept si sympa – les possibilités infinies d’apprendre de nouveaux tricks. Et le mieux dans tout ça, c’est qu’une fois que les bases sont maîtrisées, on peut progresser assez rapidement.
                    La discipline royale du jib te permet de tester toutes tes compétences en street snow. Pour faire du jib dans les rues, pense à porter les meilleures vestes de snowboard et des pantalons de snowboard adaptés, car tu vas attirer les regards ! Les rois du jib de rue viennent principalement des villes du nord, bien enneigées en hiver, qui comptent peu de stations. Le snowboard de rue demande beaucoup d’habileté en snow, mais c’est aussi une nouvelle toile où exprimer sa créativité. Sebastian Toutant (plus connu sous le nom de Seb Toots) est l’un de ces snowboarders talentueux qui parvient à faire des rues enneigées du Québec son propre snowpark. Ne nous voilons pas la face, personne ne sera jamais aussi dur à cuire. Mais on peut déjà essayer d’apprendre les rudiments du jib en s’inspirant des idées de ce roi du snow urbain.",
                        'picture' =>
                            'Jib.jpg',
                        'video' =>
                            'https://www.youtube.com/embed/5mxLMI4w_wE',
                    ],
                ],
            ],
        ];

        $faker = Faker\Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($categoryArray as $categoryData) {
            $category = new Category();
            $now = new \DateTime();

            $category->setTitle($categoryData['title']);
            $manager->persist($category);

            $figuresDataArray = $categoryData['figures'];
            foreach ($figuresDataArray as $figureData) {
                $figure = new Figure();
                $figure
                    ->setTitle($figureData['title'])
                    ->setAuthor($users[array_rand($users, 1)])
                    ->setDescription($figureData['description'])
                    ->setCategory($category)
                    ->setCreatedAt(
                        $faker->dateTimeThisMonth('now', 'Europe/Paris')
                    )
                    ->setActivatedAt($now)
                    ->setSlug(Slugify::slugify($figure->getTitle()));

                $picture = new Picture();
                $picture->setName($figureData['picture']);
                $picture->setSortOrder(1);
                $manager->persist($picture);

                $video = new Video();
                $video->setUrl($figureData['video']);
                $manager->persist($video);

                $manager->persist($figure);
                $figure->addPicture($picture);
                $figure->addVideo($video);

                for ($k = 0; $k < mt_rand(12, 20); $k++) {
                    $comment = new Comment();
                    $days = $now->diff($figure->getCreatedAt())->days;

                    $comment
                        ->setContent($faker->sentence(12))
                        ->setAuthor($users[array_rand($users, 1)])
                        ->setCreatedAt(
                            $faker->dateTimeBetween('-' . $days . ' days')
                        )
                        ->setActivatedAt($now)
                        ->setFigure($figure);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}

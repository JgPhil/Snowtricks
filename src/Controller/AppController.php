<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(FigureRepository $repo)
    {
        $figures = $repo->findBy([], ['createdAt' => 'DESC'], 6);
        $lastFigure = $figures[array_key_first($figures)];

        return $this->render('app/index.html.twig', [
            'figures' => $figures,
            'lastFigure' => $lastFigure,
        ]);
    }

    /**
     * @Route("/figure/new", name="figure_create")
     */
    public function create()
    {
        return $this->render('app/create.html.twig');
    }

    /**
     * @Route("/figure/{id}", name="trick_show")
     */
    public function show(Figure $figure)
    {
        return $this->render('app/show.html.twig', [
            'figure' => $figure,
        ]);
    }

    /**
     * @Route("/more/{offset}", name="load_more")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function sliceFigures(FigureRepository $repo, $offset = 6)
    {
        //ON RECUPERE les 6 prochaines figures  avec l'offset (Id) de la dernière figure chargée précédemment

        $sliceFigures = $repo->findBy([], ['createdAt' => 'DESC'], 6, $offset);
        $firstFigure = $sliceFigures[array_key_last($sliceFigures)];
        // ON RECUPERE le dernier index
        $offset = array_key_last($sliceFigures);

        // ON GERE les références circulaires créées par les attributs dans les entités (ex: "comments" dans l'entité "figure")
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (
                $object,
                $format,
                $context
            ) {
                return $object->getTitle();
            },
        ];
        $normalizer = new ObjectNormalizer(
            null,
            null,
            null,
            null,
            null,
            null,
            $defaultContext
        );
        $serializer = new Serializer([$normalizer], [$encoder]);
        // ON CREE les données de la réponse avec le serialzer personnalisé
        $content = 
            [
                'sliceFigures' => $sliceFigures,
                'firstFigure' => $firstFigure,
                'offset' => $offset,
            ]
        ;
        //ON RETOURNE le résultat au format json
        return new JsonResponse($content, 200);
    }
}

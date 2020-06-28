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
use Symfony\Component\Serializer\SerializerInterface;

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
        return $this->json($repo->findBy([], ['createdAt' => 'DESC'], 6, $offset), 200, [], ['groups' => 'figure_read']);
    }
}

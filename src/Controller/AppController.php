<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
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
    public function show(
        Figure $figure,
        Request $request,
        EntityManagerInterface $manager
    ) {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setFigure($figure);
            $comment->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('message', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('trick_show', [
                'id' => $figure->getId()
                ]);
        }

        return $this->render('app/show.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
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
        $figRepo = $repo->findBy([], ['createdAt' => 'DESC'], 6, $offset);
        $lastFigureId = $figRepo[array_key_last($figRepo)]->getId();

        return $this->json(
            [
                'sliceFigures' => $repo->findBy(
                    [],
                    ['createdAt' => 'DESC'],
                    6,
                    $offset
                ),
                'lastFigureId' => $lastFigureId,
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
    }
}

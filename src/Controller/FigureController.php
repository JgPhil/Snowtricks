<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Helpers\Slugify;
use App\Service\VideoHandling;
use App\Service\PictureHandling;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class FigureController extends AbstractController
{

    /**
     * @Route("/figure/new", name="figure_create")
     */
    public function create(EntityManagerInterface $em, Request $request, PictureHandling $pictureHandling, VideoHandling $videoHandling)
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('pictures')->getData();
            $videos = $form->get('videos')->getData();

            $picturesErrors = $pictureHandling->handlePictures($pictures, $figure);
            if (!empty($picturesErrors[0])) {
                $this->addFlash('danger', $picturesErrors[0]);
                return $this->redirectToRoute('figure_create');
            }
            $videoErrors = $videoHandling->handleVideos($videos, $figure);
            if (!empty($videoErrors[0])) {
                $this->addFlash('danger', $videoErrors[0]);
                return $this->redirectToRoute(('figure_create'));
            }
            $figure->setCreatedAt(new \DateTime());
            $figure->setAuthor($this->getUser());
            $figure->setSlug((Slugify::slugify($figure->getTitle())));
            $em->persist($figure);
            $em->flush();

            $this->addFlash('message', 'Votre figure a bien été ajoutée');
            return $this->redirectToRoute('home');
        }
        return $this->render('app/figure_form.html.twig', [
            'figureForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("figure/edit/{slug}", name="figure_edit", methods={"GET", "POST"})
     */
    public function edit(
        EntityManagerInterface $em,
        PictureHandling $pictureHandling,
        VideoHandling $videoHandling,
        Request $request,
        Figure $figure
    ) {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('pictures')->getData();
            $videos = $form->get('videos')->getData();

            $picturesErrors = $pictureHandling->handlePictures($pictures, $figure);
            if (!empty($picturesErrors[1])) {
                $this->addFlash('danger', $picturesErrors[0]);
                return $this->redirectToRoute('trick_show', [
                    'slug' => $figure->getSlug(),
                ]);
            }
            $videoErrors = $videoHandling->handleVideos($videos, $figure);
            if (!empty($videoErrors[0])) {
                $this->addFlash('danger', $videoErrors[0]);
                return $this->redirectToRoute('trick_show', [
                    'slug' => $figure->getSlug(),
                ]);
            }
            $figure->setLastModificationAt(new \DateTime());
            $figure->setSlug(Slugify::slugify($form->get('title')->getData()));
            $em->flush();
            $this->addFlash('message', 'Votre figure a bien été modifiée');
            return $this->redirectToRoute('home');
        }
        return $this->render('app/figure_form.html.twig', [
            'figureForm' => $form->createView(),
            'trick' => $figure,
        ]);
    }

    /**
     * @Route("/more/figures/{slug}/{maxResults}", name="load_more_figures")
     *
     * @param FigureRepository $repo
     * @return void
     */
    public function sliceActiveFigures(
        FigureRepository $repo,
        $slug,
        $maxResults
    ) {
        $figure = $repo->findOneBy(array('slug' => $slug));
        $offset = $figure->getId();

        return $this->json(
            [
                'sliceFigures' => $repo->findActiveSliceFigures(
                    $offset,
                    $maxResults
                ),
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
    }

    /**
     * @Route("/figure/{id}/next/comments/{lastCommentId}", name="load_next_comments")
     */
    public function nextComments(
        CommentRepository $repo,
        Figure $figure,
        $lastCommentId
    ) {
        $comment = $repo->find($lastCommentId);

        return $this->json(
            [
                'slice' => $repo->nextForumCommentSlice($figure, $comment),
            ],
            200,
            [],
            ['groups' => 'comment_read']
        );
    }
}

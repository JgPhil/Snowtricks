<?php

namespace App\Controller;

use Exception;
use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\FigureType;
use App\Form\CommentType;
use App\Form\ProfileType;
use App\Helpers\Slugify;
use Doctrine\ORM\EntityManager;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use App\Service\CommentHandling;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use App\Service\PictureHandling;
use App\Service\VideoHandling;



class AppController extends AbstractController
{
    const NEXT_FIGURES_MAX_RESULTS = 6;

    /**
     * @Route("/", name="home")
     */
    public function index(
        FigureRepository $repo,
        SerializerInterface $serializer
    ) {
        $figures = $repo->findActiveFigures();

        return $this->render('app/index.html.twig', [
            'figures' => $figures,
            'user' => $serializer->serialize($this->getUser(), 'json', [
                'groups' => 'user_read',
            ]),
        ]);
    }

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
            $videoErrors = $videoHandling->handleVideo($videos, $figure);
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
     * @Route("/profile", name="profile")
     */
    public function profile(EntityManagerInterface $em, Request $request, PictureHandling $pictureHandling)
    {
        $user = $this->getUser();
        $pictures = [];
        $oldPicture = $pictureHandling->userHasPicture($user);
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictures[] = $form->get('pictures')->getData();
            [$oldPicture, $errors] = $pictureHandling->handlePictures($pictures, $user);
            if (!empty($errors[0])) {
                $this->addFlash('danger', $errors[0]);
            } else {
                $em->flush();
                $this->addFlash('message', "Image modifiée avec succés");
            }
            return $this->redirectToRoute('profile');
        }
        return $this->render('app/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'oldPicture' => $oldPicture
        ]);
    }

    /**
     * @Route("/figure/{slug}", name="trick_show")
     */
    public function show(
        Figure $figure,
        Request $request,
        CommentHandling $commentHandling,
        CommentRepository $commentRepo
    ) {
        $comment = new Comment();
        $user = $this->getUser();
        $comments = $commentRepo->firstComments($figure);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentHandling->postNewComment($comment, $figure, $user);
            $this->addFlash(
                'message',
                'Votre commentaire a été posté !'
            );
            return $this->redirectToRoute('trick_show', [
                'slug' => $figure->getSlug(),
            ]);
        }
        if ($figure->getActivatedAt() != null) {
            return $this->render('app/show.html.twig', [
                'figure' => $figure,
                'commentForm' => $form->createView(),
                'comments' => $comments,
            ]);
        } else {
            $this->addFlash('danger', "Cette figure n'est pas activée");
            return $this->redirectToRoute('home');
        }
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
            $videoErrors = $videoHandling->handleVideo($videos, $figure);
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
     * @Route("/more/figures/{offset}/{maxResults}", name="load_more_figures")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function sliceActiveFigures(
        FigureRepository $repo,
        $offset,
        $maxResults
    ) {
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

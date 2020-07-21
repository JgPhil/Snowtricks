<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\FigureType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class AppController extends AbstractController
{
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
    public function create(EntityManagerInterface $em, Request $request)
    {
        $order = 1;
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Récupération des images
            $pictures = $form->get('pictures')->getData();
            $videoUrl = $form->get('videos')->getData();

            //Images 
            foreach ($pictures as $picture) {
                //nouveau nom de fichier
                $filename = md5(uniqid()) . '.' . $picture->guessExtension();
                //copie dans dossier uploads
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $filename
                );
                //stockage du nom du fichier dans la base de donnée
                $pic = new Picture();
                $pic->setName($filename);
                $pic->setSortOrder($order);

                // Ajout de l'image par cascade dans l'entité Figure -> "pictures"
                $figure->addPicture($pic);
                $order++;
            }
            //video
            if ($videoUrl) {
                $video = new Video();
                $video->setUrl($videoUrl);
                $figure->addVideo($video);
            }


            $figure->setCreatedAt(new \DateTime());
            $figure->setAuthor($this->getUser());

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
     * @Route("/figure/{id}", name="trick_show")
     */
    public function show(
        Figure $figure,
        Request $request,
        EntityManagerInterface $manager,
        CommentRepository $commentRepo
    ) {
        $comment = new Comment();
        $comments = $commentRepo->firstComments($figure);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setFigure($figure);
            $comment->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('message', 'Message posté !');
            return $this->redirectToRoute('trick_show', [
                'id' => $figure->getId(),
            ]);
        }

        return $this->render('app/show.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
            'comments' => $comments

        ]);
    }


    /**
     * @Route("figure/edit/{id}", name="figure_edit", methods={"GET", "POST"})
     */
    public function edit(
        EntityManagerInterface $em,
        Request $request,
        Figure $figure
    ) {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Récupération des images
            $pictures = $form->get('pictures')->getData();
            $videoUrl = $form->get('videos')->getData();

            //Images
            foreach ($pictures as $picture) {
                //nouveau nom de fichier
                $filename = md5(uniqid()) . '.' . $picture->guessExtension();
                //copie dans dossier uploads
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $filename
                );
                //stockage du nom du fichier dans la base de donnée
                $pic = new Picture();
                $pic->setName($filename);
                // Ajout de l'image par cascade dans l'entité Figure -> "pictures"
                $figure->addPicture($pic);
            }
            //video
            if ($videoUrl) {
                $video = new Video();
                $video->setUrl($videoUrl);
                $figure->addVideo($video);
            }

            $figure->setLastModificationAt(new \DateTime());
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
     * @Route("/more/figures/{offset}", name="load_more_figures")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function sliceActiveFigures(FigureRepository $repo, $offset)
    {
        return $this->json(
            [
                'sliceFigures' => $repo->findActiveSliceFigures($offset),
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
    }


    /**
     * @Route("/figure/{id}/next/comments/{lastCommentId}", name="load_next_comments")
     */
    public function nextComments(CommentRepository $repo, Figure $figure, $lastCommentId)
    {
        $comment = $repo->find($lastCommentId);

        return $this->json(
            [
                'slice' => $repo->nextForumCommentSlice($figure, $comment)
            ],
            200,
            [],
            ['groups' => 'comment_read']
        );
    }



    /**
     * @Route("/picture/{id}", name="picture_edit")
     */
    public function updatePicture(Picture $picture, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pictureRepo = $this->getDoctrine()->getRepository(Picture::class);
        $figureRepo = $this->getDoctrine()->getRepository(Figure::class);

        $figure = $figureRepo->find($picture->getFigure()->getId());

    
        //récupération du rang de l'image
        $order = $picture->getSortOrder();
        // effacement du fichier dans le dossier Pictures
        unlink($this->getParameter('pictures_directory') . '/' . $picture->getName());
        //Effacement de l'entrée en base de donnée de l'image 
        $em->remove($picture);



    }
}

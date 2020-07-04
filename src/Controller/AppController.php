<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\FigureType;
use App\Form\CommentType;
use App\Repository\FigureRepository;
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
        $figures = $repo->findBy([], ['createdAt' => 'DESC'], 6);
        $lastFigure = $figures[array_key_first($figures)];

        return $this->render('app/index.html.twig', [
            'figures' => $figures,
            'lastFigure' => $lastFigure,
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
                // Ajout de l'image par cascade dans l'entité Figure -> "pictures"
                $figure->addPicture($pic);
            }
            //video
            $video = new Video();
            $video->setUrl($videoUrl);
            $figure->addVideo($video);

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
            $this->addFlash('message', 'Message posté !');
            return $this->redirectToRoute('trick_show', [
                'id' => $figure->getId(),
            ]);
        }

        return $this->render('app/show.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
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

            $figure->setCreatedAt(new \DateTime());
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
     * @Route("/figure/delete/{id}", name="figure_delete")
     */
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        Figure $figure
    ) {
        if (
            $this->isCsrfTokenValid(
                'delete' . $figure->getId(),
                $request->$request->get('_token')
            )
        ) {
            $em->remove($figure);
            $em->flush();
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/delete/picture/{id}", name="picture_delete", methods={"DELETE"})
     *
     * @return void
     */
    public function deletePicture(
        Picture $picture,
        Request $request,
        EntityManagerInterface $em
    ) {
        $data = json_decode($request->getContent(), true);

        // vérification token valide
        if (
            $this->isCsrfTokenValid(
                'delete' . $picture->getId(),
                $data['_token']
            )
        ) {
            $name = $picture->getName();
            //suppression du fichier dans le dossier uploads
            unlink($this->getParameter('pictures_directory') . '/' . $name);
            // suppression de l'entrée en base
            $em->remove($picture);
            $em->flush();
            //réponse JSON
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }

    /**
     * @Route("/delete/video/{id}", name="video_delete", methods={"DELETE"})
     *
     * @return void
     */
    public function deleteVideo(
        Video $video,
        Request $request,
        EntityManagerInterface $em
    ) {
        $data = json_decode($request->getContent(), true);

        // vérification token valide
        if (
            $this->isCsrfTokenValid('delete' . $video->getId(), $data['_token'])
        ) {
            // suppression de l'entrée en base
            $em->remove($video);
            $em->flush();
            //réponse JSON
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
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
        return $this->json(
            [
                'sliceFigures' => $repo->findBy(
                    [],
                    ['createdAt' => 'DESC'],
                    6,
                    $offset
                ),
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
    }
}

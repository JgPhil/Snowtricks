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
use Doctrine\ORM\EntityManager;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

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
    public function create(EntityManagerInterface $em, Request $request)
    {
        $pictureOrder = 1; //initialisation du compteur lors d'une création de figure
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('pictures')->getData();
            $videos = $form->get('videos')->getData();

            try {
                //Images
                foreach ($pictures as $picture) {
                    if (!empty($picture)) {
                        //nouveau nom de fichier
                        $filename =
                            md5(uniqid()) . '.' . $picture->guessExtension();
                        //copie dans dossier uploads
                        $picture->move(
                            $this->getParameter('pictures_directory'),
                            $filename
                        );
                        //stockage du nom du fichier dans la base de donnée
                        $pic = new Picture();
                        $pic->setName($filename);
                        $pic->setSortOrder($pictureOrder);

                        // Ajout de l'image par cascade dans l'entité Figure -> "pictures"
                        $figure->addPicture($pic);
                        $pictureOrder++;
                    } else {
                        $this->addFlash(
                            'danger',
                            'Il y a eu un problème lors de la création de votre figure'
                        );
                        return $this->redirectToRoute('home');
                    }
                }
                if ($videos) {
                    foreach ($videos as $video) {
                        if (!empty($video)) {
                            $url = $this->checkVideoUrl($video);
                            if ($url != null) {
                                $video = new Video();
                                $video->setFigure($figure);
                                $video->setUrl($url);

                                $em->persist($video);
                                $figure->addVideo($video);
                            } else {
                                $this->addFlash(
                                    'danger',
                                    " URL de la vidéo non valide. Veuillez entrer l'URL présente telle quelle dans la barre d\'adresse de votre navigateur internet.  Par ex: https://www.youtube.com/watch?v=Pq5p6zhgzlg "
                                );
                                return $this->redirectToRoute('figure_create');
                            }
                        } else {
                            $this->addFlash(
                                'danger',
                                'Il y a eu un problème lors de la création de votre figure'
                            );
                            return $this->redirectToRoute('home');
                        }
                    }
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
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
     * @Route("/profile", name="profile")
     */
    public function profile(EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        $oldPicture = null;
        
        if (count($user->getPictures()) > 0) {
            $oldPicture = $user->getPictures()[0];
        }
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($oldPicture) {
                $em->remove($oldPicture);
            }
            $pictureData = $form->get('pictures')->getData();
            $filename = md5(uniqid()) . '.' . $pictureData->guessExtension();
            $pictureData->move(
                $this->getParameter('pictures_directory'),
                $filename
            );
            $picture = new Picture();
            $picture->setName($filename);
            $user->addPicture($picture);
            $em->persist($picture);
            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('app/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'oldPicture' => $oldPicture,
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
            //Récupération des médias
            $pictures = $form->get('pictures')->getData();
            $videos = $form->get('videos')->getData();

            try {
                // Uniquement les images AJOUTEES  => les images REMPLACEES sont gérées en AJAX
                foreach ($pictures as $picture) {
                    if (!empty($picture)) {
                        //Vérification du champ sort_order maximum en base
                        $maxOrder = $this->findPictureHighestOrder($figure);
                        //nouveau nom de fichier
                        $filename =
                            md5(uniqid()) . '.' . $picture->guessExtension();
                        //copie dans dossier uploads
                        $picture->move(
                            $this->getParameter('pictures_directory'),
                            $filename
                        );
                        //stockage du nom du fichier dans la base de donnée
                        $pic = new Picture();
                        $pic->setName($filename);
                        $pic->setSortOrder($maxOrder + 1);
                        // Ajout de l'image par cascade dans l'entité Figure -> "pictures"
                        $figure->addPicture($pic);
                    } else {
                        $this->addFlash(
                            'danger',
                            'Il y a eu un problème lors de la modification de la figure'
                        );
                        return $this->redirectToRoute('trick_show', [
                            'id' => $figure->getId(),
                        ]);
                    }
                }

                if ($videos) {
                    foreach ($videos as $video) {
                        if (!empty($video)) {
                            $url = $this->checkVideoUrl($video);
                            if ($url != null) {
                                $video = new Video();
                                $video->setFigure($figure);
                                $video->setUrl($url);

                                $em->persist($video);
                                $figure->addVideo($video);
                            } else {
                                $this->addFlash(
                                    'danger',
                                    " URL non valide. Veuillez entrer l'URL présente telle quelle dans la barre d\'adresse de votre navigateur internet.  Par ex: https://www.youtube.com/watch?v=Pq5p6zhgzlg "
                                );
                                return $this->redirectToRoute('figure_edit', [
                                    'id' => $figure->getId(),
                                ]);
                            }
                        } else {
                            $this->addFlash(
                                'danger',
                                'Il y a eu un problème lors de la modification de la figure'
                            );
                            return $this->redirectToRoute('trick_show', [
                                'id' => $figure->getId(),
                            ]);
                        }
                    }
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
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
                'sliceFigures' => $repo->findActiveSliceFigures(
                    $offset,
                    self::NEXT_FIGURES_MAX_RESULTS
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

    private function findPictureHighestOrder(Figure $figure)
    {
        $pictures = $figure->getPictures();
        // trouver le sort_order le plus élevé dans les images
        $maxOrder = 0;
        foreach ($pictures as $picture) {
            if ($picture->getsortOrder() > $maxOrder) {
                $maxOrder = $picture->getsortOrder();
            }
        }

        return $maxOrder;
    }

    /**
     * @Route("/figure/{figureId}/update/oldPicture/{oldPictureId}/oldPictureOrder/{oldPictureOrder}", name="picture_edit")
     */
    public function updatePicture(
        $figureId,
        $oldPictureId,
        $oldPictureOrder,
        Request $request
    ) {
        $em = $this->getDoctrine()->getManager();
        $pictureRepo = $this->getDoctrine()->getRepository(Picture::class);
        $figureRepo = $this->getDoctrine()->getRepository(Figure::class);
        $figure = $figureRepo->find($figureId);

        if ($oldPictureId != 'null') {
            //modification d'une image
            $oldPicture = $pictureRepo->find($oldPictureId);
            // effacement de de l'ancienne image dans le dossier Pictures
            unlink(
                $this->getParameter('pictures_directory') .
                    '/' .
                    $oldPicture->getName()
            );
            //effacement de l'entrée en base de l'ancienne image
            $em->remove($oldPicture);
            $pictureOrder = $oldPictureOrder;
        } else {
            $pictureOrder = 1; // Default picture
        }
        //Récupération et sauvegarde du fichier image
        $uploadedFile = $request->files->get('file');
        $filename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move(
            $this->getParameter('pictures_directory'),
            $filename
        );

        $newPicture = new Picture();
        $newPicture->setSortOrder($pictureOrder);
        $newPicture->setFigure($figure);
        $newPicture->setName($filename);

        $figure->addPicture($newPicture);

        $em->flush();

        return $this->json(
            [
                'message' => 'Image mise à jour',
                'newPictureFilename' => $filename,
            ],
            200
        );
    }

    /**
     * @Route("/figure/{figureId}/update/oldVideo/{oldVideoId}", name="video_edit")
     */
    public function updateVideo($figureId, $oldVideoId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $videoRepo = $this->getDoctrine()->getRepository(Video::class);
        $figureRepo = $this->getDoctrine()->getRepository(Figure::class);
        $figure = $figureRepo->find($figureId);

        $newVideoUrl = $request->getContent();

        if (
            !empty($newVideoUrl) &&
            $this->checkVideoUrl($newVideoUrl) != null
        ) {
            $video = new Video();
            $video->setFigureId($figureId);
            $video->setUrl($newVideoUrl);

            $figure->addVideo($video);

            $em->persist($video);
            $em->flush();
            // Effacement de l'ancienne vidéo
            $em->remove($videoRepo->find($oldVideoId));

            return $this->json(
                [
                    'newVideoUrl' => $newVideoUrl,
                    'message' => 'Video mise à jour',
                ],
                200
            );
        } else {
            return $this->json(
                [
                    'newVideoUrl' => $newVideoUrl,
                    'error' =>
                    "Il semble qu'il y ait un problème avec cette l'url",
                ],
                404
            );
        }
    }

    private function checkVideoUrl($video)
    {
        $url = htmlspecialchars($video->getUrl());
        $splittedUrl = explode('/', $url);

        if (
            ($splittedUrl[2] === 'www.youtube.com' ||
                $splittedUrl[2] === 'youtu.be') &&
            count($splittedUrl) < 6
        ) {
            if (preg_match('/watch?v=/', $url)) {
                $videoId = explode('=', array_pop($splittedUrl))[1];
            } else {
                $videoId = array_pop($splittedUrl);
            }
            $url = 'https://www.youtube.com/embed/' . $videoId;
        } elseif (
            ($splittedUrl[2] === 'www.dailymotion.com' ||
                $splittedUrl[2] === 'dai.ly') &&
            count($splittedUrl) < 6
        ) {
            $videoId = array_pop($splittedUrl);
            $url = 'https://www.dailymotion.com/embed/video/' . $videoId;
        } else {
            $url = null;
        }
        return $url;
    }
}

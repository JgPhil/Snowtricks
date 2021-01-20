<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Repository\UserRepository;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(FigureRepository $figRep, UserRepository $usRep, CommentRepository $comRep)
    {
        $figures = $figRep->findBy([], ['createdAt' => 'DESC'], 5);
        $users = $usRep->findBy([], ['createdAt' => 'DESC'], 5);
        $comments = $comRep->findBy([], ['createdAt' => 'DESC'], 5);



        return $this->render('admin/index.html.twig', [
            'figures' => $figures,
            'figuresCount' => count($figRep->findAll()),
            'users' => $users,
            'usersCount' => count($usRep->findAll()),
            'comments' => $comments,
            'commentsCount' => count($comRep->findAll())
        ]);
    }




    /**
     * @Route("/figure/activate/{id}", name="admin_figure_activate")
     */
    public function activateFigure(Figure $figure)
    {
        return $this->activateEntity($figure);
    }

    /**
     * @Route("/comment/activate/{id}", name="admin_comment_activate")
     */
    public function activateComment(Comment $comment)
    {
        return $this->activateEntity($comment);
    }

    /**
     * @Route("/user/activate/{id}", name="admin_user_activate")
     */
    public function activateUser(User $user)
    {
        return $this->activateEntity($user);
    }



    private function activateEntity($entity)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        if ($entity instanceof User) {
            $entity->setToken(null);
        } else {
            $entity->setActivatedAt(new \DateTime());
        }
        $em->flush();
        return new JsonResponse(['success' => 1]);
    }


    /**
     * @Route("/comment/desactivate/{id}", name="admin_comment_desactivate")
     */
    public function descativateComment(Comment $comment)
    {
        return $this->desactivateEntity($comment);
    }

    /**
     * @Route("/user/desactivate/{id}", name="admin_user_desactivate")
     */
    public function descativateUser(User $user)
    {
        return $this->desactivateEntity($user);
    }

    /**
     * @Route("/figure/desactivate/{id}", name="admin_figure_desactivate")
     */
    public function descativateFigure(Figure $figure)
    {
        return $this->desactivateEntity($figure);
    }

    /**
     * @Route("/delete/picture/{id}", name="picture_delete")
     */
    public function deletePicture(Picture $picture)
    {
        return $this->desactivateEntity($picture);
    }

    /**
     * @Route("/delete/video/{id}", name="video_delete")
     */
    public function deleteVideo(Video $video)
    {
        return $this->desactivateEntity($video);
    }



    private function desactivateEntity($entity)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $em = $this->getDoctrine()->getManager();

        if ($entity instanceof Picture) {
            $name = $entity->getName();
            //suppression du fichier dans le dossier uploads
            unlink($this->getParameter('pictures_directory') . '/' . $name);
            // suppression de l'entrée en base
            $em->remove($entity);
        } elseif ($entity instanceof Video) {
            $em->remove($entity);
        } elseif ($entity instanceof User) {
            $entity->setToken(md5(uniqid()));
        } else {
            $entity->setActivatedAt(null);
        }
        $em->flush();
        return new JsonResponse(['success' => 1]);
    }


    /**
     * @Route("/admin/next/figures/{page}", name="admin_load_next_figures")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function nextSliceFigures($page)
    {
        $repo = $this->getDoctrine()->getRepository(Figure::class);
        return $this->json(
            [
                'slice' => $repo->getList($page),
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
    }

    /**
     * @Route("/admin/next/comments/{page}", name="admin_load_next_comments")
     *
     * @param CommentRepository $repo
     * @return void
     */
    public function nextSliceComments($page)
    {
        $repo = $this->getDoctrine()->getRepository(Comment::class);
        return $this->json(
            [
                'slice' => $repo->getList($page),
            ],
            200,
            [],
            ['groups' => "comment_read"]
        );
    }


    /**
     * @Route("/admin/next/users/{page}", name="admin_load_next_users")
     *
     * @param UserRepository $repo
     * @return void
     */
    public function nextSliceUsers($page)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        return $this->json(
            [
                'slice' => $repo->getList($page),
            ],
            200,
            [],
            ['groups' => 'user_read']
        );
    }


    /**
     * @Route("/delete/figure/{slug}", name="delete_figure")
     */
    public function deleteFigure(Figure $figure, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $figure->setActivatedAt(null);
        $figure->setTitle($figure->getTitle() . '-old');
        $em->persist($figure);
        $em->flush();
        $this->addFlash('message', 'La figure a bien été effacée');
        return $this->redirectToRoute('home');
    }
}

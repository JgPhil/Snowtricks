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
        $figures = $figRep->findAll();
        $users = $usRep->findAll();
        $comments = $comRep->findAll();

        return $this->render('admin/index.html.twig', [
            'figures' => $figures,
            'users' => $users,
            'comments' => $comments
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



    
    public function activateEntity($entity)
    {
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);
        if (
            $this->isCsrfTokenValid(
                'activate' . $entity->getId(),
                $data['_token']
            )
        ) {
            if ($entity instanceof User) {
                $entity->setToken(null);
            } else {
                $entity->setActivatedAt(new \DateTime());
            }
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
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
    public function deleteVideo(Picture $video)
    {
        return $this->desactivateEntity($video);
    }



    private function desactivateEntity($entity)
    {
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);
        if (
            $this->isCsrfTokenValid(
                'delete' . $entity->getId(),
                $data['_token']
            )
        ) {
            if ($entity instanceof Picture) {
                $name = $entity->getName();
                //suppression du fichier dans le dossier uploads
                unlink($this->getParameter('pictures_directory') . '/' . $name);
                // suppression de l'entrée en base
                $em->remove($entity);
            } elseif ($entity instanceof Video) {
                $em->remove($entity);
            } elseif ($entity instanceof User) {
                $entity->setToken(null);
            } else {
                $entity->setActivatedAt(null);
            }
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }
}

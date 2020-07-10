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
    public function index(FigureRepository $figRep, UserRepository $usRep, CommentRepository $comRep, $offset=0)
    {
        $figures = $figRep->findBy([], ['createdAt' => 'DESC'] ,5 , $offset);
        $users = $usRep->findBy([], ['createdAt' => 'DESC'] ,5 , $offset);
        $comments = $comRep->findBy([], ['createdAt' => 'DESC'] ,5 , $offset);

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




    private function activateEntity($entity)
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
                $entity->setToken(md5(uniqid()));
            } else {
                $entity->setActivatedAt(null);
            }
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }

    /**
     * @Route("/admin/more/figures/{offset}", name="admin_load_more_figures")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function sliceFigures(FigureRepository $repo, $offset)
    {
        return $this->json(
            [
                'slice' => $repo->findSliceFigures($offset),
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
    }

    /**
     * @Route("/admin/more/comments/{offset}", name="admin_load_more_comments")
     *
     * @param CommentRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function sliceComments(CommentRepository $repo, $offset)
    {
        return $this->json(
            [
                'sliceComments' => $repo->findSliceComments($offset),
            ],
            200,
            [],
            ['groups' => 'comment_read']
        );
    }

    /**
     * @Route("/admin/more/users/{offset}", name="admin_load_more_users")
     *
     * @param UserRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function sliceUsers(UserRepository $repo, $offset)
    {
        return $this->json(
            [
                'sliceUsers' => $repo->findSliceUsers($offset),
            ],
            200,
            [],
            ['groups' => 'user_read']
        );
    }
}

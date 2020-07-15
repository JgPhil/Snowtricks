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
    public function index(UserRepository $usRep, CommentRepository $comRep, $offset = 0)
    {
        //$figures = $figRep->findBy([], ['id' => 'DESC'], 5, $offset);
        $users = $usRep->findBy([], ['id' => 'DESC'], 5, $offset);
        $comments = $comRep->findBy([], ['id' => 'DESC'], 5, $offset);
        $jsonResponse = json_decode($this->getFiguresPaginated(1)->getContent(), true);

        return $this->render('admin/index.html.twig', [
            'figures' => $jsonResponse['figures'],
            'pagination' => $jsonResponse['pagination'],
            'figures_count' => $jsonResponse['figures_count'],
            'users' => $users,
            'comments' => $comments
        ]);
    }


    /**
     * @Route("/figures/page/{page}", name="admin_figures_paginated")
     */
    public function getFiguresPaginated($page = 1)
    {
        $repo = $this->getDoctrine()->getRepository(Figure::class);
        $maxPerPage = '5';
        $figures_count = $repo->countTotalFigures();
        $figures = $repo->getFigures($page, $maxPerPage);

        $pagination = array(
            'page' => $page,
            'route' => "admin_figures_paginated",
            'pages_count' => ceil($figures_count / $maxPerPage),
            'route_params' => array()
        );

        return  $this->json(
            [
                'figures_count' => $figures_count,
                'figures' => $figures,
                'pagination' => $pagination
            ],
            200,
            [],
            ['groups' => 'figure_read']
        );
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
     * @Route("/admin/next/figures/{offset}", name="admin_load_next_figures")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function nextSliceFigures(FigureRepository $repo, $offset)
    {
        return $this->nextSliceEntity($repo, $offset);
    }

    /**
     * @Route("/admin/prvs/figures/{offset}", name="admin_load_prvs_figures")
     *
     * @param FigureRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function prvsSliceFigures(FigureRepository $repo, $offset)
    {
        return $this->prvsSliceEntity($repo, $offset);
    }



    /**
     * @Route("/admin/next/comments/{offset}", name="admin_load_next_comments")
     *
     * @param CommentRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function nextSliceComments(CommentRepository $repo, $offset)
    {
        return $this->nextSliceEntity($repo, $offset);
    }

    /**
     * @Route("/admin/prvs/comments/{offset}", name="admin_load_prvs_comments")
     *
     * @param CommentRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function prvsSliceComments(CommentRepository $repo, $offset)
    {
        return $this->prvsSliceEntity($repo, $offset);
    }



    /**
     * @Route("/admin/next/users/{offset}", name="admin_load_next_users")
     *
     * @param UserRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function nextSliceUsers(UserRepository $repo, $offset)
    {
        return $this->nextSliceEntity($repo, $offset);
    }

    /**
     * @Route("/admin/prvs/users/{offset}", name="admin_load_prvs_users")
     *
     * @param UserRepository $repo
     * @param [type] $offset
     * @return void
     */
    public function prvsSliceUsers(UserRepository $repo, $offset)
    {
        return $this->prvsSliceEntity($repo, $offset);
    }



    private function nextSliceEntity($repo, $offset)
    {
        $group = $this->wichGroup($repo);
        return $this->json(
            [
                'slice' => $repo->nextSlice($offset),
            ],
            200,
            [],
            ['groups' => $group]
        );
    }


    private function prvsSliceEntity($repo, $offset)
    {
        $group = $this->wichGroup($repo);
        return $this->json(
            [
                'slice' => $repo->prvsSlice($offset),
            ],
            200,
            [],
            ['groups' => $group]
        );
    }


    private function wichGroup($repo)
    {
        switch ($repo) {
            case $repo instanceof UserRepository:
                $group = "user_read";
                break;
            case $repo instanceof CommentRepository:
                $group = "comment_read";
                break;
            case $repo instanceof FigureRepository:
                $group = "figure_read";
                break;
        }
        return $group;
    }
}

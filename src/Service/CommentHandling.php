<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommentHandling
{
    private $params;
    private $em;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }

    public function postNewComment(Comment $comment, Figure $figure, User $user)
    {
        $comment->setCreatedAt(new \DateTime());
        $comment->setFigure($figure);
        $comment->setAuthor($user);
        $comment->setActivatedAt(new \DateTime());
        $this->em->persist($comment);
        $this->em->flush();
    }


}
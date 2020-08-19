<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function firstComments(Figure $figure)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->andWhere('f.figure = :figure')
            ->orderBy('f.createdAt', 'DESC')
            ->setParameter('figure', $figure)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * 
     */
    public function getList($page=1)
    {     
        $maxPerPage = 5;

        return $this->createQueryBuilder('f')
            ->setFirstResult(($page-1) * $maxPerPage)
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($maxPerPage)
            ->getQuery()
            ->getResult();
    }


    /**
     * Ajax callback "show.html.twig" infinite scroll
     */
    public function nextForumCommentSlice(Figure $figure, Comment $comment)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->andWhere('f.figure = :figure')
            ->andWhere('f.createdAt < :lastCommentDatetime')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setParameter('figure' , $figure)
            ->setParameter('lastCommentDatetime', $comment->getCreatedAt())
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

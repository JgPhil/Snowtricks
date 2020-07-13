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


    public function firstComments($figure)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->leftJoin('f.figure', 'figure', 'WITH', 'figure.id = :figureId')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setParameter('figureId' , $figure->getId())
            ->getQuery()
            ->getResult();
    }

    public function nextForumCommentSlice($figureId, $offset)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->andWhere('f.id < :offset')
            ->leftJoin('f.figure', 'figure', 'WITH', 'figure.id = :figureId')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setParameter('figureId' , $figureId)
            ->setParameter(':offset', $offset)
            ->getQuery()
            ->getResult();
    }

    public function prvsForumCommentSlice($figureId, $offset)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->andWhere('f.id > :offset')
            ->leftJoin('f.figure', 'figure', 'WITH', 'figure.id = :figureId')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setParameter('figureId' , $figureId)
            ->setParameter(':offset', $offset)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function nextSlice($offset)
    {

        return $this->createQueryBuilder('f')
            ->andWhere('f.id < :offset')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(5)
            ->setParameter('offset', $offset)
            ->getQuery()
            ->getResult();
    }


    /**
     * 
     */
    public function prvsSlice($offset)
    {

        return $this->createQueryBuilder('f')
            ->andWhere('f.id > :offset')
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(5)
            ->setParameter('offset', $offset)
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

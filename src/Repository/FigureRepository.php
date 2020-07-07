<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    /**
     * @return Figure[] Returns an array of Figure objects
     */
    public function findActiveFigures()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Figure[] Returns an array of Figure objects
     */
    public function findSliceFigures($offset)
    {   
        $count = $this->createQueryBuilder('f')
        ->select('COUNT(f)')
        ->getQuery()
        ->getSingleScalarResult();



        return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->setFirstResult($offset)
            ->setMaxResults(6)
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Figure
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

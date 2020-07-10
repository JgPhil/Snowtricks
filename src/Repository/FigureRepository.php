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
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(7)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Figure[] Returns an array of Figure objects
     */
    public function findActiveSliceFigures($offset)
    {

         return $this->createQueryBuilder('f')
            ->andWhere('f.activatedAt IS NOT NULL')
            ->andWhere('f.id < :offset')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(6)
            ->setParameter('offset', $offset)
            ->getQuery()
            ->getResult(); 
    }

    /**
     * @return Figure[] Returns an array of Figure objects
     */
    public function findSliceFigures($offset)
    {

         return $this->createQueryBuilder('f')
            ->andWhere('f.id < :offset')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(5)
            ->setParameter('offset', $offset)
            ->getQuery()
            ->getResult(); 
    }
}

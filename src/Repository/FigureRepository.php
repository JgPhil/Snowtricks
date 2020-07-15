<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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


    public function getFigures($page = 1, $maxperpage)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.createdAt', 'DESC');
        $query->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);
        return new Paginator($query);
    }

    public function countTotalFigures()
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p)');
        return $count = $query->getQuery()->getSingleScalarResult();
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
     * @return Figure[] Returns an array of Figure objects
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
}

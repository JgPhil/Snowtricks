<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
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
     * @return User[] Returns an array of User objects
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
     * @return User[] Returns an array of User objects
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
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

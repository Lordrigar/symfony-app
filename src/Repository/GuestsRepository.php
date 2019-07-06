<?php

namespace App\Repository;

use App\Entity\Guests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Guests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guests[]    findAll()
 * @method Guests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Guests::class);
    }

    // /**
    //  * @return Guests[] Returns an array of Guests objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Guests
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

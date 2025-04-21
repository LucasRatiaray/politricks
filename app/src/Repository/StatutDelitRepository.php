<?php

namespace App\Repository;

use App\Entity\StatutDelit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutDelit>
 */
class StatutDelitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutDelit::class);
    }

    //    /**
    //     * @return StatutDelit[] Returns an array of StatutDelit objects
    //     */
    //    // public function findByExampleField($value): array
    //    // {
    //    //     return $this->createQueryBuilder('s')
    //    //         ->andWhere('s.exampleField = :val')
    //    //         ->setParameter('val', $value)
    //    //         ->orderBy('s.id', 'ASC')
    //    //         ->setMaxResults(10)
    //    //         ->getQuery()
    //    //         ->getResult()
    //    //     ;
    //    // }

    //    // public function findOneBySomeField($value): ?StatutDelit
    //    // {
    //    //     return $this->createQueryBuilder('s')
    //    //         ->andWhere('s.exampleField = :val')
    //    //         ->setParameter('val', $value)
    //    //         ->getQuery()
    //    //         ->getOneOrNullResult()
    //    //     ;
    //    // }
}

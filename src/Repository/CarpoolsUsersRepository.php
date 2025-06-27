<?php

namespace App\Repository;

use App\Entity\CarpoolsUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarpoolsUsers>
 */
class CarpoolsUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarpoolsUsers::class);
    }

    public function totalCredits(): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT c.day AS date, SUM(2) AS count
         FROM App\Entity\CarpoolsUsers cu
         JOIN cu.carpool c
         WHERE cu.isConfirmed = true
         GROUP BY c.day
         ORDER BY c.day ASC'
    );

    return $query->getResult();
}

    //    /**
    //     * @return CarpoolsUsers[] Returns an array of CarpoolsUsers objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CarpoolsUsers
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

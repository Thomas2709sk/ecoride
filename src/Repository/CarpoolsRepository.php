<?php

namespace App\Repository;

use App\Entity\Carpools;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carpools>
 */
class CarpoolsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carpools::class);
    }

    public function showNextCarpools(): array
{
    return $this->createQueryBuilder('c')
        ->where('c.status = :status')
        ->setParameter('status', 'A venir')
        ->orderBy('c.day', 'ASC') // ou tout autre champ pertinent
        ->getQuery()
        ->getResult();
}

//    /**
//     * @return Carpools[] Returns an array of Carpools objects
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

//    public function findOneBySomeField($value): ?Carpools
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

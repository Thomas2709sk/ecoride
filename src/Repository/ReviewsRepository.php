<?php

namespace App\Repository;

use App\Entity\Reviews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reviews>
 */
class ReviewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reviews::class);
    }

    public function driverAverageRating(int $driverId): ?float
    {
        $qb = $this->createQueryBuilder('r')
            ->select('AVG(r.rate) as avgRate')
            ->where('r.driver = :driverId')
            ->setParameter('driverId', $driverId)
            ->andWhere('r.validate = true');

        return $qb->getQuery()->getSingleScalarResult();
    }

        public function countDriverReviews(int $driverId): int
{
    $qb = $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->where('r.driver = :driverId')
        ->andWhere('r.validate = true')
        ->setParameter('driverId', $driverId);

    return (int) $qb->getQuery()->getSingleScalarResult();
}

public function countTotalReviewsByNote(int $driverId): array
{
    $qb = $this->createQueryBuilder('r')
        ->select('r.rate, COUNT(r.id) as reviewCount')
        ->where('r.driver = :driverId')
        ->andWhere('r.validate = true')
        ->setParameter('driverId', $driverId)
        ->groupBy('r.rate');

    $result = $qb->getQuery()->getResult();

    // Préparer un tableau avec des clés de 1 à 5 pour s'assurer que toutes les notes sont couvertes
    $ratingCount = array_fill(1, 5, 0);
    foreach ($result as $row) {
        $ratingCount[$row['rate']] = (int) $row['reviewCount'];
    }

    return $ratingCount;
}

    //    /**
    //     * @return Reviews[] Returns an array of Reviews objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reviews
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

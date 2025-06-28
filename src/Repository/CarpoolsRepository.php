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


    public function carpoolDriverReviews(array $filters): array
    {

        $params = [
            'userLat'         => $filters['userLat'] ?? null,
            'userLon'         => $filters['userLon'] ?? null,
            'radiusKm'        => $filters['radiusKm'] ?? 10,
            'userArrLat'      => $filters['userArrLat'] ?? null,
            'userArrLon'      => $filters['userArrLon'] ?? null,
            'arrivalRadiusKm' => $filters['arrivalRadiusKm'] ?? 10,
            'day'             => $filters['date'] ?? null,
        ];

        $sql = "
        SELECT r.id,
            (6371 * acos(
                cos(radians(:userLat)) * cos(radians(r.startLat)) *
                cos(radians(r.startLon) - radians(:userLon)) +
                sin(radians(:userLat)) * sin(radians(r.startLat))
            )) AS distanceDeparture,
            (6371 * acos(
                cos(radians(:userArrLat)) * cos(radians(r.endLat)) *
                cos(radians(r.endLon) - radians(:userArrLon)) +
                sin(radians(:userArrLat)) * sin(radians(r.endLat))
            )) AS distanceArrival
        FROM carpools r
        WHERE r.day = :day
          AND r.startLat IS NOT NULL
          AND r.startLon IS NOT NULL
          AND r.endLat IS NOT NULL
          AND r.endLon IS NOT NULL
        HAVING distanceDeparture < :radiusKm AND distanceArrival < :arrivalRadiusKm
        ORDER BY distanceDeparture ASC
    ";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery($params);
        $ids = array_column($result->fetchAllAssociative(), 'id');
        if (empty($ids)) return [];


        $qb = $this->createQueryBuilder('r')
            ->select('r', 'AVG(rev.rate) as averageRating')
            ->join('r.driver', 'd')
            ->leftJoin('d.reviews', 'rev', 'WITH', 'rev.validate = true')
            ->andWhere('r.id IN (:ids)')
            ->groupBy('r.id')
            ->orderBy('r.day', 'ASC')
            ->setParameter('ids', $ids);

        if (isset($filters['price'])) {
            $qb->andWhere('r.price <= :price')
                ->setParameter('price', $filters['price']);
        }
        if (isset($filters['isEcological'])) {
            $qb->andWhere('r.isEcological = :isEcological')
                ->setParameter('isEcological', $filters['isEcological']);
        }
        if ($begin = ($filters['begin'] ?? null)) {
            $qb->andWhere('r.begin <= :begin')
                ->setParameter('begin', $begin);
        }
        if (!empty($filters['rate'])) {
            $qb->having('averageRating >= :rate')
                ->setParameter('rate', $filters['rate']);
        }
        if (!empty($filters['duration'])) {
            if ($filters['duration'] === 'plus_court') {
                $qb->orderBy('r.duration', 'ASC');
            } elseif ($filters['duration'] === 'plus_long') {
                $qb->orderBy('r.duration', 'DESC');
            }
        }

        $results = $qb->getQuery()->getResult();

        $carpools = [];
        foreach ($results as $item) {
            $carpool = $item[0];
            $carpool->averageRating = $item['averageRating'];
            $carpools[] = $carpool;
        }

        return $carpools;
    }

    public function NearestCarpool(
        string $day,
        float $userLat,
        float $userLon,
        float $userArrLat,
        float $userArrLon,
        int $radiusKm = 10,
        int $arrivalRadiusKm = 10
    ): ?string {
        $params = [
            'userLat'         => $userLat,
            'userLon'         => $userLon,
            'radiusKm'        => $radiusKm,
            'userArrLat'      => $userArrLat,
            'userArrLon'      => $userArrLon,
            'arrivalRadiusKm' => $arrivalRadiusKm,
            'desiredDay'      => $day,
        ];

        $sql = "
        SELECT r.day,
            ABS(DATEDIFF(r.day, :desiredDay)) AS dayDiff,
            (6371 * acos(
                cos(radians(:userLat)) * cos(radians(r.startLat)) *
                cos(radians(r.startLon) - radians(:userLon)) +
                sin(radians(:userLat)) * sin(radians(r.startLat))
            )) AS distanceDeparture,
            (6371 * acos(
                cos(radians(:userArrLat)) * cos(radians(r.endLat)) *
                cos(radians(r.endLon) - radians(:userArrLon)) +
                sin(radians(:userArrLat)) * sin(radians(r.endLat))
            )) AS distanceArrival
        FROM carpools r
        WHERE r.startLat IS NOT NULL
          AND r.startLon IS NOT NULL
          AND r.endLat IS NOT NULL
          AND r.endLon IS NOT NULL
        HAVING distanceDeparture < :radiusKm AND distanceArrival < :arrivalRadiusKm
        ORDER BY dayDiff ASC
        LIMIT 1
    ";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery($params);
        $row = $result->fetchAssociative();

        if (empty($row) || empty($row['day'])) {
            return null;
        }

        $day = $row['day'];
        if ($day instanceof \DateTimeInterface) {
            $day = $day->format('Y-m-d');
        }
        return (string)$day;
    }

    // Admin function
    public function showNextCarpools(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'A venir')
            ->orderBy('c.day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function totalCarpools(): array
    {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT r.day AS date, COUNT(r.id) AS count
             FROM App\Entity\Carpools r
             GROUP BY r.day
             ORDER BY r.day ASC'
        );

        return $query->getResult();
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

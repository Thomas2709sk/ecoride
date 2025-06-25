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

    //     public function carpoolDriverReviews(?string $day, ?string $addressStart, ?string $addressEnd, ?array $filters = null): array
    // {
    // $qb = $this->createQueryBuilder('r')
    // ->select('r', 'AVG(rev.rate) as averageRating')
    // ->join('r.driver', 'd')
    // ->leftJoin('d.reviews', 'rev', 'WITH', 'rev.validate = true') // Show reviews only if validated by admin
    // ->groupBy('r.id')
    // ->orderBy('r.day', 'ASC');

    // // Ajouter le filtre sur le jour
    // if ($day) {
    // $formattedDay = (new \DateTime($day))->format('Y-m-d');
    // $qb->andWhere('r.day = :day')
    // ->setParameter('day', $formattedDay);
    // }

    // // Ajouter le filtre sur la ville
    // if ($addressStart) {
    //     $qb->andWhere('LOWER(r.address_start) LIKE :address_start')
    //        ->setParameter('address_start', '%' . strtolower($addressStart) . '%');
    // }

    // if ($addressEnd) {
    //     $qb->andWhere('LOWER(r.address_end) LIKE :address_end')
    //        ->setParameter('address_end', '%' . strtolower($addressEnd) . '%');
    // }


    // // Filtrer par prix
    // if ($price = ($filters['price'] ?? null)) {
    // $qb->andWhere('r.price <= :price')
    // ->setParameter('price', $price);
    // }

    // // Filtrer par repas inclus
    // if (array_key_exists('isEcological', $filters) && $filters['isEcological'] !== null) {
    //     $qb->andWhere('r.isEcological = :isEcological')
    //        ->setParameter('isEcological', $filters['isEcological']);
    // }

    // // Filtrer par note moyenne minimale
    // if ($rate = ($filters['rate'] ?? null)) {
    //     $qb->andHaving('averageRating >= :rate') // Utiliser "andHaving" pour les agrégations
    //        ->setParameter('rate', $rate);
    // }

    // // Filtrer par heure de début
    // if ($begin = ($filters['begin'] ?? null)) {
    // $qb->andWhere('r.begin <= :begin')
    // ->setParameter('begin', $begin);
    // }

    // return $qb->getQuery()->getResult();
    // }

     public function carpoolDriverReviews(
        string $day,
        float $userLat,
        float $userLon,
        float $radiusKm,
        float $userArrLat,
        float $userArrLon,
        float $arrivalRadiusKm,
        ?float $price = null,
        ?bool $isEcological = null,
        ?float $rate = null,
        ?\DateTime $begin = null
    ): array {
        $params = [
            'userLat'         => $userLat,
            'userLon'         => $userLon,
            'radiusKm'        => $radiusKm,
            'userArrLat'      => $userArrLat,
            'userArrLon'      => $userArrLon,
            'arrivalRadiusKm' => $arrivalRadiusKm,
            'day'             => $day,
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
        ";

        if ($price !== null) {
            $sql .= " AND r.price <= :price";
            $params['price'] = $price;
        }
        if ($isEcological !== null) {
            $sql .= " AND r.is_ecological = :is_ecological";
            $params['is_ecological'] = $isEcological;
        }
        if ($begin !== null) {
            $sql .= " AND r.begin <= :begin";
            $params['begin'] = $begin->format('H:i:s');
        }

        // Filtre sur le rayon autour du départ ET de l'arrivée
        $sql .= " HAVING distanceDeparture < :radiusKm AND distanceArrival < :arrivalRadiusKm ORDER BY distanceDeparture ASC";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery($params);

        $ids = array_column($result->fetchAllAssociative(), 'id');

        if (empty($ids)) {
            return []; // Aucun résultat géoloc
        }

        $qb = $this->createQueryBuilder('r')
            ->select('r', 'AVG(rev.rate) as averageRating')
            ->join('r.driver', 'd')
            ->leftJoin('d.reviews', 'rev', 'WITH', 'rev.validate = true')
            ->andWhere('r.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('r.id')
            ->orderBy('r.day', 'ASC');

        if ($rate !== null) {
            $qb->having('averageRating >= :rate')
                ->setParameter('rate', $rate);
        }

        $results = $qb->getQuery()->getResult();

        // On transforme pour ne retourner que les entités Carpool avec la moyenne injectée
        $carpools = [];
        foreach ($results as $item) {
            $carpool = $item[0];
            $carpool->averageRating = $item['averageRating'];
            $carpools[] = $carpool;
        }

        return $carpools;
    }

    // **
    //  * Recherche des covoiturages selon critères principaux et filtres optionnels.
    //  *
    //  * @param string|null $day Au format 'Y-m-d'
    //  * @param string|null $addressStart
    //  * @param string|null $addressEnd
    //  * @param array|null $filters
    //  * @return Carpool[]
    //  */
    // public function carpoolDriverReviews(
    //     ?string $day,
    //     ?string $addressStart,
    //     ?string $addressEnd,
    //     ?array $filters = []
    // ): array
    // {
    //     $qb = $this->createQueryBuilder('r')
    //         ->select('r AS carpool', 'AVG(rev.rate) as averageRating')
    //         ->join('r.driver', 'd')
    //         ->leftJoin('d.reviews', 'rev', 'WITH', 'rev.validate = true')
    //         ->groupBy('r.id')
    //         ->orderBy('r.day', 'ASC');

    //     // Filtrer par jour (date)
    //     if ($day) {
    //         $qb->andWhere('r.day = :day')
    //            ->setParameter('day', $day);
    //     }

    //     // Filtrer par adresse de départ (adapter selon ton entité/propriété)
    //     if ($addressStart) {
    //         $qb->andWhere('LOWER(r.address_start) LIKE :addressStart')
    //            ->setParameter('addressStart', '%' . strtolower($addressStart) . '%');
    //     }

    //     // Filtrer par adresse d'arrivée (adapter selon ton entité/propriété)
    //     if ($addressEnd) {
    //         $qb->andWhere('LOWER(r.address_end) LIKE :addressEnd')
    //            ->setParameter('addressEnd', '%' . strtolower($addressEnd) . '%');
    //     }

    //     // --- Filtres secondaires ---
    //     $filters = $filters ?? [];

    //     // Prix maximal
    //     if (!empty($filters['price'])) {
    //         $qb->andWhere('r.price <= :price')
    //            ->setParameter('price', $filters['price']);
    //     }

    //     // Trajet écologique
    //     if (isset($filters['isEcological']) && $filters['isEcological'] !== null && $filters['isEcological'] !== '') {
    //         $qb->andWhere('r.isEcological = :isEcological')
    //            ->setParameter('isEcological', $filters['isEcological']);
    //     }

    //     // Heure de début maximale
    //     if (!empty($filters['begin'])) {
    //         $formattedBegin = $filters['begin'] instanceof \DateTimeInterface
    //             ? $filters['begin']->format('H:i:s')
    //             : (new \DateTime($filters['begin']))->format('H:i:s');
    //         $qb->andWhere('r.begin <= :begin')
    //            ->setParameter('begin', $formattedBegin);
    //     }

    //     // Note moyenne minimale
    //     if (!empty($filters['rate'])) {
    //         $qb->andHaving('averageRating >= :rate')
    //            ->setParameter('rate', $filters['rate']);
    //     }

    //     // Géolocalisation de départ/arrivée si tous les paramètres sont présents
    //     if (
    //         isset($filters['userLat'], $filters['userLon'], $filters['userArrLat'], $filters['userArrLon']) &&
    //         $filters['userLat'] !== null && $filters['userLon'] !== null &&
    //         $filters['userArrLat'] !== null && $filters['userArrLon'] !== null
    //     ) {
    //         $radiusKm = $filters['radiusKm'] ?? 10;
    //         $arrivalRadiusKm = $filters['arrivalRadiusKm'] ?? 10;

    //         $qb->addSelect("
    //             (6371 * acos(
    //                 cos(radians(:userLat)) * cos(radians(r.startLat)) *
    //                 cos(radians(r.startLon) - radians(:userLon)) +
    //                 sin(radians(:userLat)) * sin(radians(r.startLat))
    //             )) AS HIDDEN distanceDeparture
    //         ");
    //         $qb->addSelect("
    //             (6371 * acos(
    //                 cos(radians(:userArrLat)) * cos(radians(r.endLat)) *
    //                 cos(radians(r.endLon) - radians(:userArrLon)) +
    //                 sin(radians(:userArrLat)) * sin(radians(r.endLat))
    //             )) AS HIDDEN distanceArrival
    //         ");
    //         $qb->andWhere('distanceDeparture < :radiusKm')
    //            ->andWhere('distanceArrival < :arrivalRadiusKm')
    //            ->setParameter('userLat', $filters['userLat'])
    //            ->setParameter('userLon', $filters['userLon'])
    //            ->setParameter('userArrLat', $filters['userArrLat'])
    //            ->setParameter('userArrLon', $filters['userArrLon'])
    //            ->setParameter('radiusKm', $radiusKm)
    //            ->setParameter('arrivalRadiusKm', $arrivalRadiusKm);
    //     }

    //     $results = $qb->getQuery()->getResult();

    //     // Hydratation de la moyenne sur chaque objet Carpool
    //     $carpools = [];
    //     foreach ($results as $item) {
    //         $carpool = $item['carpool'];
    //         $carpool->averageRating = $item['averageRating'];
    //         $carpools[] = $carpool;
    //     }

    //     return $carpools;
    // }



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

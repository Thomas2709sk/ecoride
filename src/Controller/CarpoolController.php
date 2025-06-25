<?php

namespace App\Controller;

use App\Form\SearchFiltersForm;
use App\Repository\CarpoolsRepository;
use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carpool', name: 'app_carpool_')]
class CarpoolController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CarpoolsRepository $carpoolsRepository): Response
    {
        // Here search form for carpools later

         $carpools = $carpoolsRepository->findAll();

        return $this->render('carpool/index.html.twig', [
            'carpools' => $carpools,
        ]);
    }

    #[Route('/details/{carpoolNumber}', name: 'details')]
    public function details($carpoolNumber, CarpoolsRepository $carpoolsRepository, ReviewsRepository $reviewsRepository): Response
    {
        // search the carpools by its ID
        $carpool = $carpoolsRepository->findOneBy(['carpool_number' => $carpoolNumber]);

        // search the guide associate with the reservation
        $driver = $carpool->getDriver();

        // Use 'driverAverageRating' in the Repository to calculate the Average rating of each driver
        $averageRating = $reviewsRepository->driverAverageRating($driver->getId());

        // get total reviews
        $totalReviews = $reviewsRepository->countDriverReviews($driver->getId());

        // if carpools don't exist
        if (!$carpool) {
            throw $this->createNotFoundException('Ce covoiturage n\'existe pas');
        }

        return $this->render('carpool/details.html.twig', [
            'carpool' => $carpool,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
        ]);
    }

    //  #[Route('/results', name: 'results')]
    // public function results(
    //     Request $request,
    //     CarpoolsRepository $carpoolsRepository
    // ): Response {
    //     // get the day and city in the url of the search form
    //     $day = $request->query->get('day');
    //     $addressStart = $request->query->get('address_start');
    //     $addressEnd = $request->query->get('address_end');
    
    
    //     // add the day and city of the search form in the filters form
    //     $filtersForm = $this->createForm(SearchFiltersForm ::class, [
    //         'date' => $day ? new \DateTime($day) : null,
    //         'address_start' => $addressStart ,
    //         'address_end' => $addressEnd ,
    //     ]);
    
    //     $filtersForm->handleRequest($request);
    
    //     $filters = [
    //         'date' => $day ? new \DateTime($day) : null,
    //         'address_start' => $addressStart ,
    //         'address_end' => $addressEnd ,
    //     ];
    
    //     if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
    //         // Add filters used by user
    //         $filters = array_merge($filters, $filtersForm->getData());
    //     }
    
    //     // Update reservation with filters used by user
    //     $carpools = $carpoolsRepository->carpoolDriverReviews(
    //         $filters['date'] ? $filters['date']->format('Y-m-d') : null,
    //         $filters['address_start'], $filters['address_end'],
    //         $filters
    //     );
    
    //     // if no reservation on choosen day use 'findClosestDay' to search for the next available day 
    //     // $findClosestDay = null;
    //     // if (empty($carpools) && $day && $addressStart && $addressEnd ) {
    //     //     $findClosestDay = $carpoolsRepository->findClosestDay($day, $addressStart, $addressEnd);
    //     // }
    
    //     return $this->render('carpool/results.html.twig', [
    //         'filtersForm' => $filtersForm->createView(),
    //         'carpools' => $carpools,
    //         // 'findClosestDay' => $findClosestDay,
    //         'address_start' => $addressStart ,
    //         'address_end' => $addressEnd ,
    //     ]);
    // }

#[Route('/results', name: 'results')]
public function results(
    Request $request,
    CarpoolsRepository $carpoolsRepository
): Response {
    $day = $request->query->get('day');
    $addressStart = $request->query->get('address_start');
    $addressEnd = $request->query->get('address_end');


    $filtersForm = $this->createForm(SearchFiltersForm::class, [
        'date' => $day ? new \DateTime($day) : null,
        'address_start' => $addressStart,
        'address_end' => $addressEnd,
    ]);
    $filtersForm->handleRequest($request);

    // Récupère tous les filtres
    $filters = [
        'date' => $day ? new \DateTime($day) : null,
        'address_start' => $addressStart,
        'address_end' => $addressEnd,
        'price' => $request->query->get('price'),
        'isEcological' => $request->query->get('isEcological'),
        'rate' => $request->query->get('rate'),
        'begin' => $request->query->get('begin'),
    ];

    if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
        $filters = array_merge($filters, $filtersForm->getData());
    }

    // Géocodage DEPART
    $userLat = null;
    $userLon = null;
    $radiusKm = 10; // rayon du point de départ (modifiable)

    if ($filters['address_start']) {
        $addressEncoded = urlencode($filters['address_start']);
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=$addressEncoded";
        $opts = [
            "http" => [
                "header" => "User-Agent: MyCarpoolApp/1.0 (contact@tonsite.com)\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            $this->addFlash('error', "Erreur lors de l'appel à Nominatim pour l'adresse de départ.");
            return $this->redirectToRoute('index');
        }
        $data = json_decode($response, true);
        if (!empty($data) && isset($data[0])) {
            $userLat = (float) $data[0]['lat'];
            $userLon = (float) $data[0]['lon'];
        }
    }

    // Géocodage ARRIVEE
    $userArrLat = null;
    $userArrLon = null;
    $arrivalRadiusKm = 10; // rayon du point d'arrivée (modifiable)

    if ($filters['address_end']) {
        $addressEncoded = urlencode($filters['address_end']);
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=$addressEncoded";
        $opts = [
            "http" => [
                "header" => "User-Agent: MyCarpoolApp/1.0 (contact@tonsite.com)\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            $this->addFlash('error', "Erreur lors de l'appel à Nominatim pour l'adresse d'arrivée.");
            return $this->redirectToRoute('index');
        }
        $data = json_decode($response, true);
        if (!empty($data) && isset($data[0])) {
            $userArrLat = (float) $data[0]['lat'];
            $userArrLon = (float) $data[0]['lon'];
        }
    }

    if ($userLat === null || $userLon === null || $userArrLat === null || $userArrLon === null) {
        $this->addFlash('error', "Adresse de départ ou d'arrivée introuvable ou invalide. Merci de corriger.");
        return $this->redirectToRoute('index');
    }

    // Appel du repository avec tous les filtres attendus
    $carpools = $carpoolsRepository->carpoolDriverReviews(
        $filters['date'] ? $filters['date']->format('Y-m-d') : null,
        $userLat,
        $userLon,
        $radiusKm,
        $userArrLat,
        $userArrLon,
        $arrivalRadiusKm,
        $filters['price'] !== null ? (float)$filters['price'] : null,
        $filters['isEcological'] !== null ? (bool)$filters['isEcological'] : null,
        $filters['rate'] !== null ? (float)$filters['rate'] : null,
        $filters['begin'] !== null ? (
            $filters['begin'] instanceof \DateTimeInterface ? $filters['begin'] : new \DateTime($filters['begin'])
        ) : null
    );

    return $this->render('carpool/results.html.twig', [
        'filtersForm' => $filtersForm->createView(),
        'carpools' => $carpools,
        'address_start' => $addressStart,
        'address_end' => $addressEnd,
    ]);
}
}

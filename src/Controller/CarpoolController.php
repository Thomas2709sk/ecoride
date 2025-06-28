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
            'google_maps_api_key' => $_SERVER['GOOGLE_MAPS_API_KEY'] ?? $_ENV['GOOGLE_MAPS_API_KEY'] ?? null,
        ]);
    }

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


        $filters = [
            'date'          => $day ? new \DateTime($day) : null,
            'address_start' => $addressStart,
            'address_end'   => $addressEnd,
        ];

        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
            $filters = array_merge($filters, $filtersForm->getData());
        }

        // Géocodage des adresses (pour obtenir les coordonnées)
        $filters['userLat'] = $filters['userLon'] = $filters['userArrLat'] = $filters['userArrLon'] = null;
        $filters['radiusKm'] = 10;
        $filters['arrivalRadiusKm'] = 10;

        foreach (['address_start' => ['userLat', 'userLon'], 'address_end' => ['userArrLat', 'userArrLon']] as $addressKey => [$latKey, $lonKey]) {
            if (!empty($filters[$addressKey])) {
                $addressEncoded = urlencode($filters[$addressKey]);
                $url = "https://nominatim.openstreetmap.org/search?format=json&q=$addressEncoded";
                $opts = [
                    "http" => [
                        "header" => "User-Agent: MyCarpoolApp/1.0 (contact@tonsite.com)\r\n"
                    ]
                ];
                $context = stream_context_create($opts);
                $response = @file_get_contents($url, false, $context);
                if ($response === false) {
                    $this->addFlash('error', "Erreur lors de l'appel à Nominatim pour l'adresse.");
                    return $this->redirectToRoute('index');
                }
                $data = json_decode($response, true);
                if (!empty($data) && isset($data[0])) {
                    $filters[$latKey] = (float) $data[0]['lat'];
                    $filters[$lonKey] = (float) $data[0]['lon'];
                }
            }
        }

        // Vérification des coordonnées requises
        if (
            empty($filters['userLat']) || empty($filters['userLon']) ||
            empty($filters['userArrLat']) || empty($filters['userArrLon'])
        ) {
            $this->addFlash('error', "Adresse de départ ou d'arrivée introuvable ou invalide. Merci de corriger.");
            return $this->redirectToRoute('index');
        }

        if (!empty($filters['date']) && $filters['date'] instanceof \DateTimeInterface) {
            $filters['date'] = $filters['date']->format('Y-m-d');
        }


        $carpools = $carpoolsRepository->carpoolDriverReviews($filters);

        $usedFilters = ['rate', 'begin', 'price', 'isEcological'];
        $userFilters = (bool) array_filter(
            $usedFilters,
            fn($key) => !empty($filters[$key])
        );


        $nearestDay = null;
        if (
            empty($carpools)
            && $filters['date']
            && $filters['userLat'] && $filters['userLon']
            && $filters['userArrLat'] && $filters['userArrLon']
            && !$userFilters
        ) {
            $nearestDay = $carpoolsRepository->NearestCarpool(
                $filters['date'],
                $filters['userLat'],
                $filters['userLon'],
                $filters['userArrLat'],
                $filters['userArrLon'],
                $filters['radiusKm'] ?? 10,
                $filters['arrivalRadiusKm'] ?? 10
            );
        }

        return $this->render('carpool/results.html.twig', [
            'filtersForm'   => $filtersForm->createView(),
            'carpools'      => $carpools,
            'address_start' => $addressStart,
            'address_end'   => $addressEnd,
            'nearestDay'    => $nearestDay
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\CarpoolsRepository;
use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}

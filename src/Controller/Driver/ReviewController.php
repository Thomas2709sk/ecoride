<?php

namespace App\Controller\Driver;

use App\Repository\DriversRepository;
use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver/review', name: 'app_driver_review_')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReviewsRepository $reviewsRepository, DriversRepository $driversRepository): Response
    {
        // get User
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // get the driver ID associate with the user ID
        $driver = $driversRepository->findOneBy(['user' => $user]);

        // if driver exist
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour voir les avis.');
            return $this->redirectToRoute('app_driver_account_index');
        }

        // find reviews for each drivers and only show if validate = true
        $reviews = $reviewsRepository->findBy([
            'driver' => $driver,
            'validate' => true,
        ]);

        // Use 'driverAverageRating' in the Repository to calculate the Average rating of each driver
        $averageRating = $reviewsRepository->driverAverageRating($driver->getId());

        // get total reviews
        $totalReviews = $reviewsRepository->countDriverReviews($driver->getId());

        // get total of reviews for each rate
        $ratingCount = $reviewsRepository->countTotalReviewsByNote($driver->getId());


        return $this->render('driver/review/index.html.twig', [
            'reviews' => $reviews,
            'driver' => $driver,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'ratingCount' => $ratingCount

        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\DriversRepository;
use App\Repository\ReviewsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/review', name: 'app_review_')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', []);
    }

    #[Route('/{pseudo}/details', name: 'details')]
    public function details(string $pseudo, ReviewsRepository $reviewsRepository, DriversRepository $driversRepository, UsersRepository $usersRepository): Response
    {

        $user = $usersRepository->findOneBy(['pseudo' => $pseudo]);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $driver = $driversRepository->findOneBy(['user' => $user]);
        if (!$driver) {
            throw $this->createNotFoundException('Chauffeur non trouvé.');
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


        return $this->render('review/details.html.twig', [
            'reviews' => $reviews,
            'driver' => $driver,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'ratingCount' => $ratingCount

        ]);
    }
}

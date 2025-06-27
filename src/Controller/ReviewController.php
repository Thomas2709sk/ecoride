<?php

namespace App\Controller;

use App\Document\Review;
use App\Form\ReviewsWebsiteForm;
use App\Repository\DriversRepository;
use App\Repository\ReviewsRepository;
use App\Repository\UsersRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/review', name: 'app_review_')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, DocumentManager $dm): Response
    {
           // Create new Review object in MongoDB
     $review = new Review();
    //  Add day and time automatically for CreatedAt
     $review->setCreatedAt(new \DateTime());

    //  Create form and link with Document review
    $reviewWebForm = $this->createForm(ReviewsWebsiteForm::class, $review);
    $reviewWebForm->handleRequest($request);

    if ($reviewWebForm->isSubmitted() && $reviewWebForm->isValid()) {
        $dm->persist($review);
        $dm->flush();

        $this->addFlash('success', 'Votre avis a été envoyé avec succès.');
        return $this->redirectToRoute('app_review_index');
    }

    return $this->render('review/index.html.twig', [
        'reviewWebForm' => $reviewWebForm->createView(),
    ]);
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

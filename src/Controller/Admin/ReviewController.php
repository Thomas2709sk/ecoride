<?php

namespace App\Controller\Admin;

use App\Entity\Reviews;
use App\Repository\ReviewsRepository;
use App\Security\Voter\ReviewsVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/admin/review', name: 'app_admin_review_')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReviewsRepository $reviewsRepository, Security $security): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut valider cet avis.');
        }

         // get All the reviews
        $reviews = $reviewsRepository->findAll();

        return $this->render('admin/review/index.html.twig', [
            "reviews" => $reviews
        ]);
    }

     // Confirm good review
    #[Route('/confirm/{id}', name: 'confirm', methods: ['POST'])]
    public function confirmGoodReview(Reviews $reviews, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$authChecker->isGranted(ReviewsVoter::VALID, $reviews)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de valider cet avis.');
        }

        // set Validate to true
        $reviews->setValidate(true);


        $em->persist($reviews);
        $em->flush();

        $this->addFlash('success', 'L\'avis a été validé et est maintenant visible par le chauffeur.');

        return $this->redirectToRoute('app_admin_review_index');
    }

    // Delete review
    #[Route('/remove/{id}', name: 'remove')]
    public function removeReviews(int $id, ReviewsRepository $reviewsRepository, EntityManagerInterface $em): Response
    {

        // check if User have 'ROLE_ADMIN' or 'ROLE_STAFF'
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // get the review to delete by its ID
        $review = $reviewsRepository->find($id);

        // if review don't exist
        if (!$review) {
            $this->addFlash('error', 'Avis introuvable.');
            return $this->redirectToRoute('app_admin_reviews_index');
        }

        // delete review
        $em->remove($review);
        $em->flush();

        $this->addFlash('success', 'L\'avis a été supprimer avec succès.');

        return $this->redirectToRoute('app_admin_reviews_index');
    }
}

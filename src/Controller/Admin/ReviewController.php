<?php

namespace App\Controller\Admin;

use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}

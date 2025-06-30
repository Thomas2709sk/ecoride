<?php

namespace App\Controller\Staff;

use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/staff/account', name: 'app_staff_account')]
    public function index(ReviewsRepository $reviewRepository, Security $security): Response
    {
        // check if User have 'ROLE_STAFF'
        if (!$security->isGranted('ROLE_STAFF')) {
            throw $this->createAccessDeniedException('Seul un employé peut accéder aux avis.');
        }

        $reviewsValidateCount = $reviewRepository->countReviewsValidate();

        return $this->render('staff/account/index.html.twig', [
            'reviewsValidateCount' => $reviewsValidateCount,
        ]);
    }
}

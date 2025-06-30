<?php

namespace App\Controller\Admin;

use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/admin/account', name: 'app_admin_account')]
    public function index(Security $security, ReviewsRepository $reviewRepository): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut accéder aux avis.');
        }

           $reviewsValidateCount = $reviewRepository->countReviewsValidate();

        return $this->render('admin/account/index.html.twig', [
            'reviewsValidateCount' => $reviewsValidateCount,
        ]);
    }
}

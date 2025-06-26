<?php

namespace App\Controller\Admin;

use App\Entity\CarpoolsUsers;
use App\Entity\Reviews;
use App\Entity\Users;
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
        if (
            !$security->isGranted('ROLE_ADMIN')
            && !$security->isGranted('ROLE_STAFF')
        ) {
            throw $this->createAccessDeniedException('Seuls un administrateur ou un employé peuvent accéder aux avis.');
        }

        // get All the reviews
        $reviews = $reviewsRepository->findAll();

        return $this->render('admin/review/index.html.twig', [
            "reviews" => $reviews
        ]);
    }

    // Confirm good review
    #[Route('/confirm/{id}', name: 'confirm', methods: ['POST'])]
    public function confirmGoodReview($id, ReviewsRepository $reviewsRepository, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker): Response
    {
        $review = $reviewsRepository->find($id);

        if (!$review) {
            $this->addFlash('error', 'Avis introuvable.');
            return $this->redirectToRoute('app_admin_review_index');
        }

        // check if User have 'ROLE_ADMIN' or 'ROLE_STAFF'
        if (!$authChecker->isGranted(ReviewsVoter::VALID, $review)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de valider cet avis.');
        }

        // set Validate to true
        $review->setValidate(true);


        $em->persist($review);
        $em->flush();

        $this->addFlash('success', 'L\'avis a été validé et est maintenant visible par le chauffeur.');

        return $this->redirectToRoute('app_admin_review_index');
    }

    // Delete review > 3 stars
    #[Route('/remove/{id}', name: 'remove')]
    public function removeReviews(int $id, ReviewsRepository $reviewsRepository, EntityManagerInterface $em,  AuthorizationCheckerInterface $authChecker): Response
    {

        // get the review to delete by its ID
        $review = $reviewsRepository->find($id);

        // if review don't exist
        if (!$review) {
            $this->addFlash('error', 'Avis introuvable.');
            return $this->redirectToRoute('app_admin_review_index');
        }

        // check if User have 'ROLE_ADMIN' or 'ROLE_STAFF'
        if (!$authChecker->isGranted(ReviewsVoter::DELETE, $review)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cet avis.');
        }

        // delete review
        $em->remove($review);
        $em->flush();

        $this->addFlash('success', 'L\'avis a été supprimer avec succès.');

        return $this->redirectToRoute('app_admin_reviews_index');
    }

    // Confirm review < 3 stars
    #[Route('/confirm/bad/{id}', name: 'bad', methods: ['POST'])]
    public function confirmBadReview(
        Reviews $review,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker
    ): Response {

        $carpool = $review->getCarpool();
        if (!$carpool) {
            $this->addFlash('error', 'Aucun covoiturage associé à cet avis.');
            return $this->redirectToRoute('app_admin_review_index');
        }


        if (!$authChecker->isGranted(ReviewsVoter::VALID, $review)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de valider cet avis.');
        }

        $user = $review->getUser();

        $carpoolUser = $em->getRepository(CarpoolsUsers::class)->findOneBy([
            'user' => $user,
            'carpool' => $carpool,
        ]);
        if (!$carpoolUser) {
            $this->addFlash('error', 'Impossible de trouver le covoiturage lier au passager.');
            return $this->redirectToRoute('app_admin_review_index');
        }


        $review->setValidate(true);
        $carpoolUser->setIsConfirmed(true);

        if ($carpool->getStatus() !== 'Vérification par la plateforme') {
            $this->addFlash('error', 'Cet avis ne peut pas être confirmé.');
            return $this->redirectToRoute('app_admin_review_index');
        }

        $price = $carpool->getPrice();
        $platformPrice = 2;
        $driverCredits = $price - $platformPrice;

        $driver = $carpool->getDriver();
        $carpoolDriver = $driver->getUser();


        $admin = $em->getRepository(Users::class)->find(1);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles(), true)) {
            throw new \Exception('L\'utilisateur avec l\'ID 1 n\'est pas un administrateur.');
        }


        if ($carpoolUser->isConfirmed()) {
            $carpoolDriver->setCredits($carpoolDriver->getCredits() + $driverCredits);
            $admin->setCredits($admin->getCredits() + $platformPrice);
        }


        $allPassengerConfirmed = true;
        foreach ($carpool->getCarpoolsUsers() as $cu) {
            if (!$cu->isConfirmed()) {
                $allPassengerConfirmed = false;
                break;
            }
        }
        if ($allPassengerConfirmed) {
            $carpool->setStatus('Confirmé');
        }

        $em->persist($review);
        $em->persist($carpoolUser);
        $em->persist($carpoolDriver);
        $em->persist($admin);
        $em->persist($carpool);
        $em->flush();

        $this->addFlash('success', 'L\'avis négatif a été validé, est visible par le chauffeur et les crédits transférés.');
        return $this->redirectToRoute('app_admin_review_index');
    }

    //  Delete reviews < 3 stars but still send credits to driver and admin
    #[Route('/remove/bad/{id}', name: 'remove_bad', methods: ['POST'])]
    public function removeBadReview(
        Reviews $review,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker
    ): Response {

        $carpool = $review->getCarpool();
        if (!$carpool) {
            $this->addFlash('error', 'Aucun covoiturage associé à cet avis.');
            return $this->redirectToRoute('app_admin_review_index');
        }


        if (!$authChecker->isGranted(ReviewsVoter::DELETE, $review)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cet avis.');
        }


        $user = $review->getUser();


        $carpoolUser = $em->getRepository(CarpoolsUsers::class)->findOneBy([
            'user' => $user,
            'carpool' => $carpool,
        ]);
        if (!$carpoolUser) {
            $this->addFlash('error', 'Impossible de trouver la réservation de ce passager pour ce covoiturage.');
            return $this->redirectToRoute('app_admin_review_index');
        }

        $carpoolUser->setIsConfirmed(true);

        if ($carpool->getStatus() !== 'Vérification par la plateforme') {
            $this->addFlash('error', 'Ce trajet ne peut pas être confirmé.');
            return $this->redirectToRoute('app_admin_review_index');
        }

        $price = $carpool->getPrice();
        $platformPrice = 2;
        $driverCredits = $price - $platformPrice;

        $driver = $carpool->getDriver();
        $carpoolDriver = $driver->getUser();

        $admin = $em->getRepository(Users::class)->find(1);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles(), true)) {
            throw new \Exception('L\'utilisateur avec l\'ID 1 n\'est pas un administrateur.');
        }

        if ($carpoolUser->isConfirmed()) {
            $carpoolDriver->setCredits($carpoolDriver->getCredits() + $driverCredits);
            $admin->setCredits($admin->getCredits() + $platformPrice);
        }

        $allPassengerConfirmed = true;
        foreach ($carpool->getCarpoolsUsers() as $cu) {
            if (!$cu->isConfirmed()) {
                $allPassengerConfirmed = false;
                break;
            }
        }
        if ($allPassengerConfirmed) {
            $carpool->setStatus('Confirmé');
        }

        $em->remove($review);

        $em->persist($carpoolUser);
        $em->persist($carpoolDriver);
        $em->persist($admin);
        $em->persist($carpool);
        $em->flush();

        $this->addFlash('success', 'L\'avis a été supprimé, mais les crédits ont bien été transférés.');

        return $this->redirectToRoute('app_admin_review_index');
    }
}

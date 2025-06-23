<?php

namespace App\Controller\User;

use App\Entity\Carpools;
use App\Entity\CarpoolsUsers;
use App\Entity\Reviews;
use App\Form\ReviewDriverForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/review', name: 'app_user_review_')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('user/review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }

    #[Route('/send/{carpoolNumber}', name: 'send', methods: ['GET','POST'])]
    public function send(
        string $carpoolNumber,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour laisser un avis.');
        }

         // find carpool
        $carpool = $em->getRepository(Carpools::class)->findOneBy(['carpool_number' => $carpoolNumber]);
        if (!$carpool) {
            $this->addFlash('error', 'Le covoiturage est introuvable.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // search the reservation with its ID
        $carpoolUser = $em->getRepository(CarpoolsUsers::class)->findOneBy([
            'user' => $user,
            'carpool' => $carpool,
        ]);

        // if User is part of the carpool
        if (!$carpool->getUser()->contains($user)) {
            $this->addFlash('error', 'Vous ne pouvez pas laisser un avis pour ce covoiturage.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // if review from User already exist
        $reviewExist = $em->getRepository(Reviews::class)->findOneBy([
            'carpool' => $carpool,
            'user' => $user
        ]);

        if ($reviewExist) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour ce covoiturage.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // if no review create a new Reviews object
        $review = new Reviews();
        $review->setUser($user);
        $review->setDriver($carpool->getDriver());
        $review->setCarpool($carpool);

        // Create form
        $reviewForm = $this->createForm(ReviewDriverForm::class, $review);

        $reviewForm->handleRequest($request);
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            $carpoolUser->setIsAnswered(true);
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été envoyé avec succès.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        return $this->render('user/review/send.html.twig', [
            'reviewForm' => $reviewForm,
            'carpool' => $carpool,
        ]);
    }

     #[Route('/bad/{carpoolNumber}', name: 'bad', methods: ['GET','POST'])]
    public function sendBandReviews(
        string $carpoolNumber,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour laisser un avis.');
        }

         // find carpool
        $carpool = $em->getRepository(Carpools::class)->findOneBy(['carpool_number' => $carpoolNumber]);
        if (!$carpool) {
            $this->addFlash('error', 'Le covoiturage est introuvable.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // if User is part of the carpool
        if (!$carpool->getUser()->contains($user)) {
            $this->addFlash('error', 'Vous ne pouvez pas laisser un avis pour ce covoiturage.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        $carpoolUser = $em->getRepository(CarpoolsUsers::class)->findOneBy([
            'user' => $user,
            'carpool' => $carpool,
        ]);

        // if review from User already exist
        $reviewExist = $em->getRepository(Reviews::class)->findOneBy([
            'carpool' => $carpool,
            'user' => $user
        ]);

        if ($reviewExist) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour ce covoiturage.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // if no review create a new Reviews object
        $review = new Reviews();
        $review->setUser($user);
        $review->setDriver($carpool->getDriver());
        $review->setCarpool($carpool);

        // Create form
        $reviewForm = $this->createForm(ReviewDriverForm::class, $review);

        $reviewForm->handleRequest($request);
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {

            $carpoolUser->setIsAnswered(true);
            $carpool->setStatus('Vérification par la plateforme');
            $em->persist($carpool);
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été envoyé avec succès.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        return $this->render('user/review/bad.html.twig', [
            'reviewForm' => $reviewForm,
            'carpool' => $carpool,
        ]);
    }
}

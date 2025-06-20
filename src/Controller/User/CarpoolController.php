<?php

namespace App\Controller\User;

use App\Entity\Users;
use App\Entity\Carpools;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/carpool', name: 'app_user_carpool_')]
class CarpoolController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Here will show User Carpools

        return $this->render('user/carpool/index.html.twig', [

        ]);
    }

    #[Route('/book/{id}', name: 'book')]
    public function book(Carpools $carpool, EntityManagerInterface $em, SendEmailService $mail): Response
    {
        // look if user is logged and have 'ROLE_USER'
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        // get User
        /** @var Users $user */
        $user = $this->getUser();

        // If User book reservation
        // Déduct the user credits depending of reservations price
        $user->setCredits($user->getCredits() - $carpool->getPrice());

        // Add the user to the reservation
        $carpool->addUser($user);

        // Add the reservation to the user account
        $user->addCarpool($carpool);

        // edit places available of the reservation
        $carpool->setPlacesAvailable($carpool->getPlacesAvailable() - 1);

        $em->persist($carpool);
        $em->persist($user);
        $em->flush();

         // Send mail to the user
        $mail->send(
            'no-reply@ecoride.fr',
            $user->getEmail(),
            'Confirmation de votre réservation',
            'reservation_confirm',
            compact('user', 'carpool') // ['user'=> $user]
        );

        $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');

        return $this->redirectToRoute('app_user_carpool_index');
    }
}

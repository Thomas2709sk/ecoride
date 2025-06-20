<?php

namespace App\Controller\User;

use App\Entity\Users;
use App\Entity\Carpools;
use App\Repository\CarpoolsRepository;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\Voter\CarpoolsVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/user/carpool', name: 'app_user_carpool_')]
class CarpoolController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
         /** @var Users $user */
        $user = $this->getUser();

         // get the reservation of the User
         $carpools = $user->getCarpools();

        return $this->render('user/carpool/index.html.twig', [
            'carpools' => $carpools,
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

    #[Route('cancel/{id}', name: 'cancel', methods: ['POST'])]
    public function cancel(
        $id,
        CarpoolsRepository $carpoolsRepository, 
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker,): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        $carpool = $carpoolsRepository->find($id);
        if (!$carpool) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        if (!$authChecker->isGranted(CarpoolsVoter::CANCEL, $carpool)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit d\'annuler cette réservations.');
        }

        // if user cancel get credits back
        $user->setCredits($user->getCredits() + $carpool->getPrice());

        // remove user from reservation
        $carpool->removeUser($user);

        // edit number of places available
        $carpool->setPlacesAvailable($carpool->getPlacesAvailable() + 1);

        // Save in DB
        $em->persist($carpool);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a été annulée avec succès.');

        return $this->redirectToRoute('app_user_carpool_index');
    }
}

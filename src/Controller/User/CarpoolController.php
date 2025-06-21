<?php

namespace App\Controller\User;

use App\Entity\Users;
use App\Entity\Carpools;
use App\Entity\CarpoolsUsers;
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

    #[Route('/details/{id}', name: 'details')]
    public function details($id, CarpoolsRepository $carpoolsRepository): Response
    {
        // search the carpools by its ID
        $carpool = $carpoolsRepository->findOneBy(['id' => $id]);

        // if carpools don't exist
        if (!$carpool) {
            throw $this->createNotFoundException('Ce covoiturage n\'existe pas');
        }

        return $this->render('user/carpool/details.html.twig', [
            'carpool' => $carpool,
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
        AuthorizationCheckerInterface $authChecker,
    ): Response {
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

    // if passenger finish the carpool with OUI (YES)
    #[Route('/confirm/{id}', name: 'confirm')]
    public function confirm(
        $id,
        Carpools $carpool,
        EntityManagerInterface $em) 
    {
        $user = $this->getUser();

        // search the reservation with its ID
        $carpoolUser = $em->getRepository(CarpoolsUsers::class)->findOneBy([
            'user' => $user,
            'carpool' => $carpool,
        ]);

        // if User is part of the carpool
        if (!$carpool->getUser()->contains($user)) {
            $this->addFlash('error', 'Réservation invalide ou non autorisée.');
            return $this->redirectToRoute('app_user_carpool_index');
        }


        if ($carpoolUser->isConfirmed()) {
            $this->addFlash('info', 'Vous avez déjà confirmé ce covoiturage.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // if user can confirm the end of the reservation
        if ($carpool->getStatus() !== 'Terminé') {
            $this->addFlash('error', 'Cette réservation ne peut pas être confirmée.');
            return $this->redirectToRoute('app_user_carpool_index');
        }

        // update the carpools_user for each user after confirm
        $carpoolUser->setIsConfirmed(true);
        $em->persist($carpoolUser);

        // Count credits for driver and admin
        $price = $carpool->getPrice();
        $platformPrice = 2;
        $driverCredits = $price - $platformPrice;

        $driver = $carpool->getDriver();

        // find admin by its id and if 'ROLE_ADMIN'
        $admin = $em->getRepository(Users::class)->find(1);

        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles(), true)) {
            throw new \Exception('L\'utilisateur avec l\'ID 1 n\'est pas un administrateur.');
        }

        // get the guide by its user id
        $carpoolDriver = $driver->getUser();

        // Send credits (uniquement maintenant que l'on sait que ce n'est pas déjà confirmé)
        $carpoolDriver->setCredits($carpoolDriver->getCredits() + $driverCredits);
        $admin->setCredits($admin->getCredits() + $platformPrice);

        // if all passenger of the carpool isConfirmed = true
        $allPassengerConfirmed = true;
        foreach ($carpool->getCarpoolsUsers() as $carpoolUsers) {
            if (!$carpoolUsers->isConfirmed()) {
                $allPassengerConfirmed = false;
                break;
            }
        }

        // Change carpool status as Confirmé
        if ($allPassengerConfirmed) {
            $carpool->setStatus('Confirmé');
            $em->persist($carpool);
        }

        // Save to DB
        $em->persist($driver);
        $em->persist($admin);
        $em->flush();

        $this->addFlash('success', 'Merci de votre confirmation, vous pouvez laisser un avis à votre chauffeur ! !');
        return $this->redirectToRoute('app_user_carpool_index');
    }
}

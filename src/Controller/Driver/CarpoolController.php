<?php

namespace App\Controller\Driver;

use App\Entity\Carpools;
use App\Entity\Cars;
use App\Form\CreateCarpoolForm;
use App\Form\DriverCarForm;
use App\Repository\CarpoolsRepository;
use App\Repository\DriversRepository;
use App\Security\Voter\CarpoolsVoter;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/driver/carpool', name: 'app_driver_carpool_')]
class CarpoolController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(DriversRepository $driversRepository): Response
    {

         /** @var Users $user */
        $user = $this->getUser();

        $driver = $driversRepository->findOneBy(['user' => $user]);
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour voir les covoiturages.');
            return $this->redirectToRoute('app_user_account_index');
        }

        // get the carpools of the User
        $carpools = $driver->getCarpools();

        return $this->render('driver/carpool/index.html.twig', [
            'carpools' => $carpools,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em, DriversRepository $driversRepository): Response
    {

        $user = $this->getUser();

        $car = new Cars();

        // For the driver to add another Car in the create Carpool page
        $carForm = $this->createForm(DriverCarForm::class, $car);

        $carForm->handleRequest($request);

        // Create new Carpool object
        $carpool = new Carpools();

        // get the driver ID associate with the user ID
        $driver = $driversRepository->findOneBy(['user' => $user]);

        // if driver don't exist
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour créer un covoiturage.');
            return $this->redirectToRoute('app_user_account_index');
        }

        // get the car the driver created in this account
        $driverCar = $driver->getCars();

        // Create form
        $carpoolForm = $this->createForm(CreateCarpoolForm::class, $carpool, [
            'driver_car' => $driverCar
        ]);

        $carpoolForm->handleRequest($request);

        // For the carForm
        if ($carForm->isSubmitted() && $carForm->isValid()) {

            $car->setDriver($driver);

            $em->persist($car);
            $em->flush();

            $this->addFlash('success', 'Votre véhicule a été ajouté avec succès.');

            return $this->redirectToRoute('app_driver_carpool_create');
        }
        //  For the carpoolForm
        if ($carpoolForm->isSubmitted() && $carpoolForm->isValid()) {
            
            // Associate the driver to the carpool
            $carpool->setDriver($driver);

            $car = $carpool->getCar();

            // set 'PlacesAvailable' for the carpool depending of 'seats' of the car driver choose
            $carpool->setPlacesAvailable($car->getSeats() ?? 0);

            // The Carpool will have a random number who not follow
            $carpool->setCarpoolNumber('COV#' . bin2hex(random_bytes(4)));
          
            $em->persist($carpool);
            $em->flush();

            $this->addFlash('success', 'Le covoiturage a été ajoutée.');
            return $this->redirectToRoute('app_driver_carpool_index');
        }

        return $this->render('driver/carpool/create.html.twig', [
            'carpoolForm' => $carpoolForm->createView(),
            'carForm' => $carForm->createView(),
        ]);
    }

    #[Route('/details/{carpoolNumber}', name: 'details')]
    public function details($carpoolNumber, DriversRepository $driversRepository, CarpoolsRepository $carpoolsRepository): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        $driver = $driversRepository->findOneBy(['user' => $user]);
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour voir vos covoiturages.');
            return $this->redirectToRoute('app_user_account_index');
        }

        // Get the carpool associate with the driver
        $carpool = $carpoolsRepository->findOneBy(['carpool_number' => $carpoolNumber]);
        if (!$carpool || $carpool->getDriver() !== $driver) {
            $this->addFlash('error', 'Ce covoiturage ne vous appartient pas.');
            return $this->redirectToRoute('index');
        }


        $car = $driver->getCars();

        return $this->render('driver/carpool/details.html.twig', [
            'carpool' => $carpool,
            'car' => $car
        ]);
    }

    #[Route('cancel/{id}', name: 'cancel', methods: ['POST'])]
    public function cancel(
        $id,
        CarpoolsRepository $carpoolsRepository, 
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker,
        SendEmailService $mail): Response
    {
        /** @var Users $user */
        $ownerUser = $this->getUser();

        $carpool = $carpoolsRepository->find($id);
        if (!$carpool) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        if (!$authChecker->isGranted(CarpoolsVoter::CANCEL, $carpool)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit d\'annuler ce covoiturage.');
        }

       // Get all the Users from the reservations
        $users = $carpool->getUser(); 
   
        // send mail to each users
           // set credits back to each users
        foreach ($users as $user) {
            $user->setCredits($user->getCredits() + $carpool->getPrice());
            $mail->send(
                'no-reply@ecoride.fr',
                $user->getEmail(),
                'Annulation de votre covoiturage',
                'carpool_cancel',
                compact('user', 'carpool') // ['user'=> $user, 'reservation' => $reservation]
            );
        }
                // delete reservation
                $em->remove($carpool);
                $em->flush();

        $this->addFlash('success', 'Votre covoiturage a été annulée avec succès.');

        return $this->redirectToRoute('app_driver_carpool_index');
    }

    #[Route('/start/{id}', name: 'start', methods: ['POST'])]
    public function startReservation(Carpools $carpool, EntityManagerInterface $em): Response
    {
        // check if the guide is the owner of the carpool
        if ($this->getUser() !== $carpool->getDriver()->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas démarrer ce covoiturage.');
        }
    
        // Check carpool status
        if ($carpool->getStatus() !== 'A venir') {
            $this->addFlash('error', 'Ce covoiturage ne peut pas être démarrée.');
            return $this->redirectToRoute('app_guide_account_index');
        }

    
        // Update status when driver start carpool
        $carpool->setStatus('En cours');
        $em->persist($carpool);
        $em->flush();
    
        $this->addFlash('success', 'Le covoiturage a été démarrée avec succès.');
    
        return $this->redirectToRoute('app_driver_carpool_index');
    }

    #[Route('/end/{id}', name: 'end', methods: ['POST'])]
    public function endReservation(Carpools $carpool, EntityManagerInterface $em,  SendEmailService $mail): Response
    {
        // check if the driver is the owner of the carpool
        if ($this->getUser() !== $carpool->getDriver()->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas finir ce covoiturage.');
        }
    
        // Check carpool sstatus
        if ($carpool->getStatus() !== 'En cours') {
            $this->addFlash('error', 'Ce covoiturage ne peut pas être terminée.');
            return $this->redirectToRoute('app_driver_carpool_index');
        }

         // Get all the Users from the reservations
         $users = $carpool->getUser(); 

         // send mail to each users
         foreach ($users as $user) {
            $mail->send(
                'no-reply@ecoride.fr',
                $user->getEmail(),
                'Fin de votre covoiturage',
                'carpool_end',
                compact('user', 'carpool') // ['user'=> $user, 'reservation' => $reservation]
            );
        }
    
        // Update status
        $carpool->setStatus('Terminé');
        $em->persist($carpool);
        $em->flush();
    
        $this->addFlash('success', 'Le covoiturage a été terminée avec succès.');
    
        return $this->redirectToRoute('app_driver_carpool_index');
    }
}

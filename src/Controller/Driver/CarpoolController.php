<?php

namespace App\Controller\Driver;

use App\Entity\Carpools;
use App\Entity\Cars;
use App\Form\CreateCarpoolForm;
use App\Form\DriverCarForm;
use App\Repository\CarpoolsRepository;
use App\Repository\DriversRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, DriversRepository $driversRepository, CarpoolsRepository $carpoolsRepository): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        $driver = $driversRepository->findOneBy(['user' => $user]);
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour voir vos covoiturages.');
            return $this->redirectToRoute('app_user_account_index');
        }

        // Get the carpool associate with the driver
        $carpool = $carpoolsRepository->find($id);
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
}

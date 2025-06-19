<?php

namespace App\Controller\Driver;

use App\Entity\Carpools;
use App\Form\CreateCarpoolForm;
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
    public function index(): Response
    {
        // Afficher covoiturages

        return $this->render('driver/carpool/index.html.twig', []);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em, DriversRepository $driversRepository): Response
    {

        $user = $this->getUser();

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

        if ($carpoolForm->isSubmitted() && $carpoolForm->isValid()) {
            
            // Associate the driver to the carpool
            $carpool->setDriver($driver);

            $car = $carpool->getCar();

            // set 'PlacesAvailable' for the carpool depending of 'seats' of the car driver choose
            $carpool->setPlacesAvailable($car->getSeats() ?? 0);

            $carpool->setCarpoolNumber('COV#' . bin2hex(random_bytes(4)));
          
            $em->persist($carpool);
            $em->flush();

            $this->addFlash('success', 'Le covoiturage a été ajoutée.');
            return $this->redirectToRoute('app_driver_carpool_index');
        }

        return $this->render('driver/carpool/create.html.twig', [
            'carpoolForm' => $carpoolForm->createView(),
        ]);
    }
}

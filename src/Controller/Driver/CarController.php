<?php

namespace App\Controller\Driver;

use App\Entity\Cars;
use App\Repository\DriversRepository;
use App\Repository\CarsRepository;
use App\Form\DriverCarForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\Voter\CarsVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/driver/car', name: 'app_driver_car_')]
class CarController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em, DriversRepository $driversRepository): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        // Create new Cars object
        $car = new Cars();

        $carForm = $this->createForm(DriverCarForm::class, $car);

        // get the driver ID associate with the user ID
        $driver = $driversRepository->findOneBy(['user' => $user]);

        // if driver don't exist
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour ajouter un véhicule.');
            return $this->redirectToRoute('app_user_account_index');
        }

        $carForm->handleRequest($request);

        if ($carForm->isSubmitted() && $carForm->isValid()) {

            $car->setDriver($driver);

            $em->persist($car);
            $em->flush();

            $this->addFlash('success', 'Votre véhicule a été ajouté avec succès.');

            return $this->redirectToRoute('app_driver_car_index');
        }

        return $this->render('driver/car/index.html.twig', [
            'carForm' => $carForm->createView(),
        ]);
    }

    #[Route('/list', name: 'list')]
    public function list(DriversRepository $driversRepository): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        $driver = $driversRepository->findOneBy(['user' => $user]);
        if (!$driver) {
            $this->addFlash('error', 'Vous devez être un chauffeur pour voir vos véhicules.');
            return $this->redirectToRoute('app_user_account_index');
        }

        // get the cars of the User
        $cars = $driver->getCars();

        return $this->render('driver/car/list.html.twig', [
            'cars' => $cars
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function editCars(
        $id,
        CarsRepository $carsRepository,
        AuthorizationCheckerInterface $authChecker,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $car = $carsRepository->find($id);
        if (!$car) {
            throw $this->createNotFoundException('Véhicule non trouvé.');
        }

        // Vérify if the Driver is the owner
        if (!$authChecker->isGranted(CarsVoter::EDIT, $car)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier ce véhicule.');
        }

        $carForm = $this->createForm(DriverCarForm::class, $car);
        
        $carForm->handleRequest($request);

        if ($carForm->isSubmitted() && $carForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le véhicule a été modifié.');
            return $this->redirectToRoute('app_driver_car_list');
        }

        return $this->render('driver/car/edit.html.twig', [
            'carForm' => $carForm->createView(),
            'car' => $car,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function deleteCars(
        $id,
        CarsRepository $carsRepository,
        AuthorizationCheckerInterface $authChecker,
        EntityManagerInterface $em
    ): Response {
        $car = $carsRepository->find($id);
        if (!$car) {
            throw $this->createNotFoundException('Véhicule non trouvé.');
        }

        if (!$authChecker->isGranted(CarsVoter::DELETE, $car)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer ce véhicule.');
        }

        $em->remove($car);
        $em->flush();

        $this->addFlash('success', 'Le véhicule a été supprimé.');
        return $this->redirectToRoute('app_driver_car_list');


        return $this->render('driver/car/edit.html.twig', [
        ]);
    }
}

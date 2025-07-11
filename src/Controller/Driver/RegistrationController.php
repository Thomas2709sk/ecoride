<?php

namespace App\Controller\Driver;

use App\Entity\Cars;
use App\Entity\Users;
use App\Entity\Drivers;
use App\Form\DriverCarForm;
use App\Form\DriverPreferencesForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/driver/registration', name: 'app_driver_registration_')]
class RegistrationController extends AbstractController
{

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/preferences', name: 'preferences')]
    public function addPreferences(Request $request, EntityManagerInterface $em): Response
    {
        // get User
        /** @var Users $user */
        $user = $this->getUser();

        $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);

        // Create new Drivers object, create driver_id and link it to the User
        if (!$driver) {
            $driver = new Drivers();
            $driver->setUser($user);
        }


        $driverForm = $this->createForm(DriverPreferencesForm::class, $driver);

        $driverForm->handleRequest($request);

        if ($driverForm->isSubmitted() && $driverForm->isValid()) {

            $em->persist($driver);
            $em->flush();

            return $this->redirectToRoute('app_driver_registration_car');
        }


        return $this->render('driver/registration/index.html.twig', [
            'driverForm' => $driverForm->createView(),
        ]);
    }

    #[Route('/car', name: 'car')]
    public function addCar(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        // Get the driver link to the User
        $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);

        if (!$driver) {
            throw $this->createNotFoundException('Profil chauffeur introuvable.');
        }

        $car = new Cars();
        $car->setDriver($driver);

        $carForm = $this->createForm(DriverCarForm::class, $car);

        $carForm->handleRequest($request);

        if ($carForm->isSubmitted() && $carForm->isValid()) {
            $em->persist($car);

            // Add ["ROLE_DRIVER"] when User give all information needed
            $roles = $user->getRoles();
            if (!in_array('ROLE_DRIVER', $roles)) {
                $roles[] = 'ROLE_DRIVER';
                $user->setRoles($roles);
            }

            $em->flush();

            $this->addFlash('success', 'Votre inscription comme chauffeur a été validée.');


            $this->tokenStorage->setToken(
                new UsernamePasswordToken($user, 'main', $user->getRoles())
            );


            return $this->redirectToRoute('app_driver_account_index');
        }

        return $this->render('driver/registration/car.html.twig', [
            'carForm' => $carForm->createView(),
        ]);
    }
}

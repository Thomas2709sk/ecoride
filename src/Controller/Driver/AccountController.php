<?php

namespace App\Controller\Driver;

use App\Entity\Drivers;
use App\Entity\Users;
use App\Form\DriverPreferencesForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver/account', name: 'app_driver_account_')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
         /** @var Users $user */
        $user = $this->getUser();

        $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);

        $driverForm = $this->createForm(DriverPreferencesForm::class, $driver);

        $driverForm->handleRequest($request);

        if ($driverForm->isSubmitted() && $driverForm->isValid()) {

            $em->persist($driver);
            $em->flush();

            $this->addFlash('success', 'Votre profil de chauffeur a été mis à jour.');

            return $this->redirectToRoute('app_driver_account_index');
        }

        return $this->render('driver/account/index.html.twig', [
            'driverForm' => $driverForm->createView(),
        ]);
    }
}

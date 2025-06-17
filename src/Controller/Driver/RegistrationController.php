<?php

namespace App\Controller\Driver;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/driver/registration', name: 'app_driver_registration')]
    public function index(): Response
    {
        return $this->render('driver/registration/index.html.twig', [
        ]);
    }
}

<?php

namespace App\Controller\Driver;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver/account', name: 'app_driver_account_')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('driver/account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}

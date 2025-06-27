<?php

namespace App\Controller\Staff;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/staff/account', name: 'app_staff_account')]
    public function index(): Response
    {
        return $this->render('staff/account/index.html.twig', [

        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/lega/lcontroller', name: 'app_legal')]
    public function index(): Response
    {
        return $this->render('legal/index.html.twig', [

        ]);
    }
}

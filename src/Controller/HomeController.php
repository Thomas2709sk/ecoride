<?php

namespace App\Controller;

use App\Form\SearchCarpoolForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index( Request $request): Response
    {
        $searchForm = $this->createForm(SearchCarpoolForm::class);

        $searchForm->handleRequest($request);

         if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();

            // Redirect to results page with the date and address in the URL from the method 'GET'
            return $this->redirectToRoute('app_carpool_results', [
                'day' => $data['date'] ? $data['date']->format('Y-m-d') : null,
                'address_start' => $data['address_start'],
                'address_end' => $data['address_end'],
            ]);
        }

        return $this->render('home/index.html.twig', [
            'searchForm' => $searchForm->createView(),
        ]);
    }
}

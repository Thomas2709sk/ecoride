<?php

namespace App\Controller;

use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(DocumentManager $dm): Response
    {
                // find 2 reviews
        $reviews = $dm->getRepository(Review::class)->findBy([], null, 2);

        return $this->render('home/index.html.twig', [
            'reviews' => $reviews
        ]);
    }
}

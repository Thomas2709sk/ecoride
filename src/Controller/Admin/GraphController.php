<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GraphController extends AbstractController
{
    #[Route('/admin/graph', name: 'app_admin_graph')]
    public function index(): Response
    {
        return $this->render('admin/graph/index.html.twig', [
            'controller_name' => 'GraphController',
        ]);
    }
}

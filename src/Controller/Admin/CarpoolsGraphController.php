<?php

namespace App\Controller\Admin;

use App\Repository\CarpoolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/graph', name: 'app_admin_graph_')]
class CarpoolsGraphController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Security $security): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut accéder aux avis.');
        }

        return $this->render('admin/graph/carpools.html.twig', [

        ]);
    }
     #[Route('/carpools', name: 'carpools', methods: ['GET'])]
        public function totalCredits(
           CarpoolsRepository $carpoolsRepository,
            Security $security 
        ): JsonResponse {
            // Check if User have 'ROLE_ADMIN'
            if (!$security->isGranted('ROLE_ADMIN')) {
                return new JsonResponse(['error' => 'Access Denied'], 403);
            }
    
            // set local to French
            setlocale(LC_TIME, 'fr_FR.UTF-8');
    
            // Use reservationByDay in the reservation Repository to count the reservation for each day
            $carpools = $carpoolsRepository->totalCarpools();
            
            // set the reservation per month and day
            $aggregatedData = [];
            foreach ($carpools as $carpool) {
                if ($carpool['date'] instanceof \DateTime) {
                    $monthYear = $carpool['date']->format('Y-m'); // "2025-03"
                    $day = $carpool['date']->format('d');
                    
                    // if month don't exist
                    if (!isset($aggregatedData[$monthYear])) {
                        $aggregatedData[$monthYear] = [];
                    }
    
                    $aggregatedData[$monthYear][$day] = ($aggregatedData[$monthYear][$day] ?? 0) + $carpool['count'];
                }
            }
            
            // set the data
            $data = [];
            foreach ($aggregatedData as $monthYear => $days) {
                // Create a new date
                $date = new \DateTime($monthYear . "-01"); 
                // number of day for each month
                $daysInMonth = $date->format('t'); 
    
                // set the month in French
                $formatter = new \IntlDateFormatter(
                    'fr_FR',
                    \IntlDateFormatter::LONG,
                    \IntlDateFormatter::NONE,
                    null,
                    null,
                    'MMMM yyyy'
                );
                $monthInFrench = $formatter->format($date);
                
                // Create labels and count
                $labels = [];
                $counts = [];
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    // for the day < 10 had 0
                    $day = str_pad($i, 2, '0', STR_PAD_LEFT); 
                    $labels[] = $day;
                    $counts[] = $days[$day] ?? 0;
                }
    
                $data[] = [
                    'month' => $monthInFrench,
                    'labels' => $labels,
                    'counts' => $counts,
                ];
            }
            
            return new JsonResponse($data);
        }
}

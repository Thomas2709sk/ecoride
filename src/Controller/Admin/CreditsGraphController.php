<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Repository\CarpoolsRepository;
use App\Repository\CarpoolsUsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/credits/graph', name: 'app_admin_credits_graph_')]
class CreditsGraphController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Security $security): Response
    {
         $admin = $security->getUser();

    // Check if User have 'ROLE_ADMIN'
    if ($admin instanceof Users && in_array('ROLE_ADMIN', $admin->getRoles())) {
        $credits = $admin->getCredits();
    } else {
        $credits = 0;
    }

        return $this->render('admin/credits_graph/index.html.twig', [
            'credits_total' => $credits,
        ]);
    }

     #[Route('/credits', name: 'credits', methods: ['GET'])]
    public function totalCredits(

        CarpoolsUsersRepository $carpoolsUsersRepository,
        Security $security
    ): JsonResponse {
        // Check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access Denied'], 403);
        }

        $credits = $carpoolsUsersRepository->totalCredits();

        // get the credits by day and month
        $aggregatedData = $this->CreditsByMonth($credits);

        // get the formated data for the front
        $data = $this->formatData($aggregatedData);

        return new JsonResponse($data);
    }

    /**
     * Credits per month and day
     */
    private function CreditsByMonth(array $credits): array
    {
        $aggregatedData = [];
        foreach ($credits as $credit) {
            if (!isset($credit['date']) || !$credit['date'] instanceof \DateTime) {
                continue;
            }

            $monthYear = $credit['date']->format('Y-m');
            $day = $credit['date']->format('d'); 

            // If month don't exist
            if (!isset($aggregatedData[$monthYear])) {
                $aggregatedData[$monthYear] = [];
            }

            $aggregatedData[$monthYear][$day] = ($aggregatedData[$monthYear][$day] ?? 0) + $credit['count'];
        }

        return $aggregatedData;
    }

    private function formatData(array $aggregatedData): array
    {
        $data = [];
        foreach ($aggregatedData as $monthYear => $days) {
            // if at least 1 day have more than 0 credits
            if (array_sum($days) === 0) {
                continue;
            }

            $date = new \DateTime($monthYear . '-01');
            $daysInMonth = $date->format('t');

            $monthFrench = $this->frenchFormat($date);

            [$labels, $counts] = $this->createLabelsAndCounts($days, $daysInMonth);

            $data[] = [
                'month' => $monthFrench,
                'labels' => $labels,
                'counts' => $counts,
            ];
        }

        return $data;
    }

    /**
     * Use French format for date
     */
    private function frenchFormat(\DateTime $date): string
    {
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            null,
            null,
            'MMMM yyyy'
        );

        return $formatter->format($date);
    }

    /**
     * Create labels for each month
     */
    private function createLabelsAndCounts(array $days, int $daysInMonth): array
    {
        $labels = [];
        $counts = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = str_pad($i, 2, '0', STR_PAD_LEFT);
            $labels[] = $day;
            $counts[] = $days[$day] ?? 0;
        }

        return [$labels, $counts];
    }
}

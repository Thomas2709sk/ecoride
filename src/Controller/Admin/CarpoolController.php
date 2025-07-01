<?php

namespace App\Controller\Admin;

use App\Form\CreateCarpoolForm;
use App\Repository\CarpoolsRepository;
use App\Security\Voter\CarpoolsVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/admin/carpool', name: 'app_admin_carpool_')]
class CarpoolController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CarpoolsRepository $carpoolsRepository, Security $security): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut accéder aux avis.');
        }

        $showCarpools = $carpoolsRepository->showNextCarpools();

        return $this->render('admin/carpool/index.html.twig', [
            'carpools' => $showCarpools,
        ]);
    }

    #[Route('/edit/{carpoolNumber}', name: 'edit', methods: ['GET', 'POST'])]
    public function editCarpool(
        $carpoolNumber,
        CarpoolsRepository $carpoolsRepository,
        AuthorizationCheckerInterface $authChecker,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $carpools = $carpoolsRepository->findOneBy(['carpool_number' => $carpoolNumber]);
        if (!$carpools) {
            throw $this->createNotFoundException('Covoiturage non trouvé.');
        }
        $driver = $carpools->getDriver(); // Supposons que cette méthode te donne le chauffeur, qui a une relation avec User ou Driver

        $driverCars = $driver ? $driver->getCars() : [];

        // Vérify if the Driver is the owner
        if (!$authChecker->isGranted(CarpoolsVoter::EDIT, $carpools)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier ce covoiturage.');
        }

        $carpoolForm = $this->createForm(CreateCarpoolForm::class, $carpools, [
            'driver_car' => $driverCars,
        ]);

        $carpoolForm->handleRequest($request);

        if ($carpoolForm->isSubmitted() && $carpoolForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le covoiturage a été modifié.');
            return $this->redirectToRoute('app_admin_carpool_index');
        }

        return $this->render('admin/carpool/edit.html.twig', [
            'carpoolForm' => $carpoolForm->createView(),
            'carpools' => $carpools,
            'driver_car' => $driverCars,
                        'google_maps_api_key' => $_SERVER['GOOGLE_MAPS_API_KEY'] ?? $_ENV['GOOGLE_MAPS_API_KEY'] ?? null,
        ]);
    }
}

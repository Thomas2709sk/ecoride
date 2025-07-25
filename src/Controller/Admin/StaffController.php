<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\AddStaffForm;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/staff', name: 'app_admin_staff_')]
class StaffController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository, Security $security): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut accéder aux avis.');
        }

        $users = $usersRepository->showStaffAndAdmins();

        return $this->render('admin/staff/index.html.twig', [
            'users' => $users
        ]);
    }

     #[Route('/create', name: 'create')]
    public function createStaff(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $em): Response
    {
        // Create new Users object to add new staff
        $user = new Users();

        // Create form
        $form = $this->createForm(AddStaffForm::class, $user);

        // handle form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();

            $user->setRoles(['ROLE_STAFF']);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //  set verified to true for staff
            $user->setIsVerified(true);

            // save to DB
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Employé inscrit');
            return $this->redirectToRoute('app_admin_staff_index');
        }

        return $this->render('admin/staff/create.html.twig', [
            'staffForm' => $form,
        ]);
    }
}

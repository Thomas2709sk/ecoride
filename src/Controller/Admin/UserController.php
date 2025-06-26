<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use App\Security\Voter\UsersVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/admin/user', name: 'app_admin_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository,  Security $security): Response
    {
        // check if User have 'ROLE_ADMIN'
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut accéder aux avis.');
        }

         // get all users by their ID
        $users = $usersRepository->showMembers();


        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function removeUser(int $id, UsersRepository $usersRepository, EntityManagerInterface $em,  AuthorizationCheckerInterface $authChecker): Response
    {
        // find the user to remove with its ID
        $user = $usersRepository->find($id);

        // if user don't exist
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        // check if user have 'ROLE_ADMIN'
        if (!$authChecker->isGranted(UsersVoter::DELETE, $user)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cet utilisateur.');
        }

        // Remove user
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimer avec succès.');

        return $this->redirectToRoute('app_admin_user_index');
    }
}

<?php

namespace App\Controller\User;

use App\Entity\Drivers;
use App\Entity\Users;
use App\Form\EditUserAccountForm;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


#[Route('/user/account', name: 'app_user_account_')]
class AccountController extends AbstractController
{

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {

        // get User
        /** @var Users $user */
        $user = $this->getUser();

        $accountForm = $this->createForm(EditUserAccountForm::class, $user);

        $accountForm->handleRequest($request);

        if (!$accountForm->isSubmitted()) {
            $accountForm->get('role')->setData($user->getDisplayedRole());
        }

        if ($accountForm->isSubmitted() && $accountForm->isValid()) {

            // upload picture for user profile picture
            $featuredImage = $accountForm->get('photo')->getData();

            if ($featuredImage instanceof UploadedFile) {

                // Use function square in Service -> PictureService
                $image = $pictureService->square($featuredImage, 'users', 40);

                $user->setPhoto($image);
            }
            // Get user role
            $role = $accountForm->get('role')->getData();

            $currentRole = $user->getDisplayedRole();

            // If User change role 
            if ($role !== $currentRole) {
                // Call private function "RoleChange"
                return $this->RoleChange($user, $role, $em);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('app_user_account_index');
        }

        return $this->render('user/account/index.html.twig', [
            'accountForm' => $accountForm->createView(),
        ]);
    }

    private function RoleChange(Users $user, string $role, EntityManagerInterface $em): ?Response
    {
        $userRoles = $user->getRoles();

        if ($role === 'chauffeur') {

            $user->setIsPassenger(false);

            $this->addFlash('success', "Vous êtes maintenant uniquement chauffeur.");

            $em->flush();

            if (!in_array('ROLE_DRIVER', $userRoles, true)) {
                return $this->redirectToRoute('app_driver_registration_preferences');
            }

            return $this->redirectToRoute('app_user_account_index');
        } elseif ($role === 'chauffeur/passager') {

            $user->setIsPassenger(true);

            $this->addFlash('success', "Vous êtes maintenant chauffeur et passager.");

            $em->flush();

            if (!in_array('ROLE_DRIVER', $userRoles, true)) {
                return $this->redirectToRoute('app_driver_registration_preferences');
            }

            return $this->redirectToRoute('app_user_account_index');
        } elseif ($role === 'passager') {

            $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);
            // Delete driver_id 
            if ($driver) {
                $em->remove($driver);
            }

            // if user choose to be a passenger again , Delete ["ROLE_DRIVER"]
            $roles = $user->getRoles();
            $roles = array_diff($roles, ['ROLE_DRIVER']);
            $user->setRoles($roles);

            // Can be a passenger again
            $user->setIsPassenger(true);


            $em->flush();

            $this->tokenStorage->setToken(
                new UsernamePasswordToken($user, 'main', $user->getRoles())
            );


            $this->addFlash('success', "Vous êtes maintenant uniquement passager.");
            return $this->redirectToRoute('app_user_account_index');
        }
        return null;
    }
}

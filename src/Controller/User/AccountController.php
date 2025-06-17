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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[Route('/user/account', name: 'app_user_account_')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        // get User
        /** @var Users $user */
        $user = $this->getUser();

        // Create form
        $accountForm = $this->createForm(EditUserAccountForm::class, $user);

        $accountForm->handleRequest($request);

        // if form is valid
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

            if (in_array($role, ['chauffeur', 'chauffeur/passager']) && !in_array('ROLE_DRIVER', $user->getRoles(), true)) {

                $em->flush();

                return $this->redirectToRoute('app_driver_registration_preferences');
            } elseif ($role === 'passager') {

                $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);
                // Supprimer le driver si il existe
                if ($driver) {
                    $em->remove($driver);
                }

                // Retirer le rôle DRIVER
                $roles = $user->getRoles();
                $roles = array_diff($roles, ['ROLE_DRIVER']);
                $user->setRoles($roles);
                $em->flush();

                // Rafraîchir le token pour mettre à jour les rôles dans la session
                $this->container->get('security.token_storage')->setToken(
                    new UsernamePasswordToken($user, 'main', $user->getRoles())
                );

                $this->addFlash('success', "Vous êtes maintenant uniquement passager.");
                return $this->redirectToRoute('app_user_account_index');
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
}

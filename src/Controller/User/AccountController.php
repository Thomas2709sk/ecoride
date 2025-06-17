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

            if ($role === 'chauffeur') {
                // if User choose guide in the account form
                $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);
                if (!$driver) {
                    // Create new object Drivers
                    $driver = new Drivers();
                    // User get a Driver ID
                    $driver->setUser($user);
                    $em->persist($driver);
                }
            } elseif ($role === 'passager') {
                // If User is a driver and chosse 'passager'
                $driver = $em->getRepository(Drivers::class)->findOneBy(['user' => $user]);
                // Remove driver ID 
                if ($driver) {
                    $em->remove($driver);
                }
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

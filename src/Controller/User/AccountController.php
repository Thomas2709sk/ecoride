<?php

namespace App\Controller\User;

use App\Form\EditUserAccountForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/account', name: 'app_user_account_')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // get User
        $user = $this->getUser();

        // Create form
        $accountForm = $this->createForm(EditUserAccountForm::class, $user);

        $accountForm->handleRequest($request);

        // if form is valid
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
        

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

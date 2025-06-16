<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationForm;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\JWTService;
use App\Service\SendEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, JWTService $jwt, SendEmailService $mail): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            // Generate token
            // Header
             $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // Generate Token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // Envoyer l'email
            $mail->send(
                'no-reply@ecoride.fr',
                $user->getEmail(),
                'Activation de votre compte sur Ecoride',
                'register',
                compact('user', 'token') // ['user'=> $user, 'token'=>$token]
            );

            $this->addFlash('success', 'Utilisateur inscrit, veuillez cliquer sur le lien reçu pour confirmer votre adresse e-mail');

            // Send mail

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

     #[Route('/verif/{token}', name: 'verify_user')]
    public function verifUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $em): Response
    {
        // Verifi is token is valid
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            // is valid
            // get the data (payload)
            $payload = $jwt->getPayload($token);

            // get User
            $user = $usersRepository->find($payload['user_id']);

            // Verify User and if isVerified is still false
            if($user && !$user->isVerified()){
                $user->setIsVerified(true);
                $em->flush();

                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('index');
            }
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');

    }
}

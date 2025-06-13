<?php

namespace App\Command;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create admin',
)]

class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create new admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pseudo = $io-> ask('Nom de l\'admin');
        $email = $io -> ask('email de l\'admin','admin@example.com');
        $password = $io-> askHidden('Mot de passe de l\'admin');

        $user = new Users();
        $user->setPseudo($pseudo);
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
       $user->setisVerified(true);
        try{
            $this->em->persist($user);
            $this->em->flush();
            
            $io->success('Admin successfully created!');
            return Command::SUCCESS;
        } catch(\Exception $e) {
            $io->error('Cannot create admin :' . $e->getMessage());

            return Command::FAILURE;
        }

    }
}
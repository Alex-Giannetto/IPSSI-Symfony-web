<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'create-admin';
    private $entityManager;
    private $encoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create an user admin using an email and a password')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $io->note(sprintf('Create a User for email: %s', $email));

        $user = new User();
        $user->setEmail($email);
        $passwordEncoder = $this->encoder->encodePassword($user, $password);
        $user->setPassword($passwordEncoder);
        $user->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('You have created an Admin User');
    }
}

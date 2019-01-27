<?php

namespace App\Command;

use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CountVideosByUserCommand extends Command
{
    protected static $defaultName = 'count-videos-by-user';
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Enter the email adress of the user you want count his video')
            ->addArgument('email', InputArgument::REQUIRED, 'Email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->userManager->getUserbyEmail($email);
        if ($user !== null) {
            $videos = $user->getVideos();
            $io->success(sprintf('This user have %s videos', count($videos)));
        } else {
            $io->error("This user doesn't exist");
        }


    }
}

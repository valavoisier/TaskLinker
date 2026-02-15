<?php

namespace App\Command;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:2fa:manage',
    description: 'Activer ou désactiver la 2FA pour un utilisateur',
)]
class TwoFactorManageCommand extends Command
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('action', InputArgument::REQUIRED, 'Action: enable ou disable');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $action = $input->getArgument('action');

        $employee = $this->employeeRepository->findOneBy(['email' => $email]);

        if (!$employee) {
            $io->error("Utilisateur avec l'email {$email} non trouvé.");
            return Command::FAILURE;
        }

        if ($action === 'disable') {
            $employee->setGoogleAuthenticatorSecret(null);
            $employee->setIsTwoFactorEnabled(false);
            $this->em->flush();
            $io->success("2FA désactivée pour {$email}");
        } elseif ($action === 'enable') {
            $io->warning('Pour activer la 2FA, utilisez l\'interface web /2fa/setup');
        } else {
            $io->error('Action invalide. Utilisez "enable" ou "disable"');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

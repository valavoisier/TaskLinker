<?php

namespace App\Command;

use App\Entity\Employee;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:encrypt-secrets',
    description: 'Chiffre tous les secrets Google Authenticator stockés en clair dans la base de données'
)]
class EncryptSecretsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EncryptionService $encryptionService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Affiche ce qui serait fait sans modifier la base')
            ->setHelp('Cette commande chiffre tous les secrets Google Authenticator non chiffrés dans la base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        if ($dryRun) {
            $io->note('Mode DRY-RUN : aucune modification ne sera effectuée');
        }

        $employees = $this->entityManager->getRepository(Employee::class)->findAll();
        $encrypted = 0;
        $skipped = 0;
        $errors = 0;

        $io->title('Chiffrement des secrets Google Authenticator');
        $io->progressStart(count($employees));

        foreach ($employees as $employee) {
            $secret = $employee->getGoogleAuthenticatorSecret();

            if ($secret === null || $secret === '') {
                $skipped++;
                $io->progressAdvance();
                continue;
            }

            // Vérifier si déjà chiffré
            // Secret non chiffré = base32 (A-Z, 2-7 uniquement)
            // Secret chiffré = base64 (contient minuscules, +, /)
            $isBase32 = preg_match('/^[A-Z2-7]+=*$/', $secret);
            if (!$isBase32) {
                // Déjà chiffré
                $skipped++;
                $io->progressAdvance();
                continue;
            }

            try {
                if (!$dryRun) {
                    $encryptedSecret = $this->encryptionService->encrypt($secret);
                    $employee->setGoogleAuthenticatorSecret($encryptedSecret);
                    $this->entityManager->persist($employee);
                }
                $encrypted++;
            } catch (\Exception $e) {
                $io->error(sprintf(
                    'Erreur pour l\'employé %s (%s): %s',
                    $employee->getEmail(),
                    $employee->getId(),
                    $e->getMessage()
                ));
                $errors++;
            }

            $io->progressAdvance();
        }

        if (!$dryRun && $encrypted > 0) {
            $this->entityManager->flush();
        }

        $io->progressFinish();
        $io->newLine();

        $io->success([
            sprintf('Traitement terminé : %d employés traités', count($employees)),
            sprintf('✓ Secrets chiffrés : %d', $encrypted),
            sprintf('○ Secrets ignorés (déjà chiffrés ou vides) : %d', $skipped),
            sprintf('✗ Erreurs : %d', $errors),
        ]);

        if ($dryRun && $encrypted > 0) {
            $io->warning('Mode DRY-RUN : Relancez sans --dry-run pour effectuer le chiffrement');
        }

        return Command::SUCCESS;
    }
}

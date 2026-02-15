<?php

namespace App\Command;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-decryption',
    description: 'Teste le déchiffrement automatique des secrets 2FA'
)]
class TestDecryptionCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $employees = $this->entityManager
            ->getRepository(Employee::class)
            ->createQueryBuilder('e')
            ->where('e.googleAuthenticatorSecret IS NOT NULL')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        if (empty($employees)) {
            $io->warning('Aucun employé avec secret 2FA trouvé');
            return Command::SUCCESS;
        }

        $io->title('Test du déchiffrement automatique');
        $io->table(
            ['Email', 'Secret déchiffré', 'Longueur', 'Format'],
            array_map(function (Employee $employee) {
                $secret = $employee->getGoogleAuthenticatorSecret();
                $isBase32 = preg_match('/^[A-Z2-7]+=*$/', $secret ?? '');
                return [
                    $employee->getEmail(),
                    substr($secret ?? 'NULL', 0, 16) . '...',
                    strlen($secret ?? ''),
                    $isBase32 ? '✓ Base32' : '✗ Chiffré'
                ];
            }, $employees)
        );

        $io->success('Le déchiffrement fonctionne correctement si les secrets apparaissent en Base32');

        return Command::SUCCESS;
    }
}

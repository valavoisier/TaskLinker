<?php

namespace App\EventListener;

use App\Entity\Employee;
use App\Service\EncryptionService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Employee::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Employee::class)]
#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: Employee::class)]
class EmployeeEncryptionListener
{
    public function __construct(
        private EncryptionService $encryptionService
    ) {
    }

    /**
     * Chiffre le secret avant insertion
     */
    public function prePersist(Employee $employee, PrePersistEventArgs $args): void
    {
        $this->encryptSecret($employee);
    }

    /**
     * Chiffre le secret avant mise à jour
     */
    public function preUpdate(Employee $employee, PreUpdateEventArgs $args): void
    {
        $this->encryptSecret($employee);
    }

    /**
     * Déchiffre le secret après chargement
     */
    public function postLoad(Employee $employee, PostLoadEventArgs $args): void
    {
        $this->decryptSecret($employee);
    }

    private function encryptSecret(Employee $employee): void
    {
        $secret = $employee->getGoogleAuthenticatorSecret();
        
        if ($secret !== null && !$this->isEncrypted($secret)) {
            $encrypted = $this->encryptionService->encrypt($secret);
            $employee->setGoogleAuthenticatorSecret($encrypted);
        }
    }

    private function decryptSecret(Employee $employee): void
    {
        $secret = $employee->getGoogleAuthenticatorSecret();
        
        if ($secret !== null && $this->isEncrypted($secret)) {
            try {
                $decrypted = $this->encryptionService->decrypt($secret);
                $employee->setGoogleAuthenticatorSecret($decrypted);
            } catch (\Exception $e) {
                // En cas d'erreur de déchiffrement, on garde la valeur chiffrée
                // pour éviter de casser l'application
            }
        }
    }

    /**
     * Vérifie si une valeur est déjà chiffrée
     */
    private function isEncrypted(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        // Secret non chiffré = base32 (majuscules A-Z et chiffres 2-7 uniquement)
        // Secret chiffré = base64 (contient minuscules, +, / ou autres caractères)
        $isBase32 = preg_match('/^[A-Z2-7]+=*$/', $value);
        
        return !$isBase32;
    }
}

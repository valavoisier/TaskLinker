<?php

namespace App\Service;

use Exception;

class EncryptionService
{
    private string $key;

    public function __construct(string $appSecret)
    {
        // Dériver une clé de 32 octets à partir du APP_SECRET
        $this->key = hash('sha256', $appSecret, true);
    }

    /**
     * Chiffre une donnée sensible
     */
    public function encrypt(?string $data): ?string
    {
        if ($data === null || $data === '') {
            return null;
        }

        try {
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $encrypted = sodium_crypto_secretbox($data, $nonce, $this->key);
            
            // Combine nonce + encrypted et encode en base64
            return base64_encode($nonce . $encrypted);
        } catch (Exception $e) {
            throw new Exception('Erreur lors du chiffrement : ' . $e->getMessage());
        }
    }

    /**
     * Déchiffre une donnée sensible
     */
    public function decrypt(?string $encryptedData): ?string
    {
        if ($encryptedData === null || $encryptedData === '') {
            return null;
        }

        try {
            $decoded = base64_decode($encryptedData, true);
            
            if ($decoded === false) {
                throw new Exception('Données chiffrées invalides');
            }

            $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
            $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
            
            $decrypted = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->key);
            
            if ($decrypted === false) {
                throw new Exception('Échec du déchiffrement');
            }

            return $decrypted;
        } catch (Exception $e) {
            throw new Exception('Erreur lors du déchiffrement : ' . $e->getMessage());
        }
    }
}

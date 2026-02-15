# Documentation - Chiffrement des secrets 2FA avec Sodium

## 📋 Vue d'ensemble

Le système de chiffrement protège les secrets Google Authenticator stockés en base de données en utilisant **libsodium**, une bibliothèque cryptographique moderne et sécurisée.

## 🔐 Algorithme utilisé

- **Algorithme** : `crypto_secretbox` (XSalsa20-Poly1305)
- **Chiffrement** : XSalsa20 (chiffrement par flux)
- **Authentification** : Poly1305 (MAC - Message Authentication Code)
- **Taille de clé** : 32 octets (256 bits)
- **Taille du nonce** : 24 octets (192 bits)

### Pourquoi Sodium ?

✅ **Sécurité** : Résistant aux attaques par canal auxiliaire
✅ **Rapidité** : Optimisé pour la performance
✅ **Simplicité** : API simple et difficile à mal utiliser
✅ **Standard** : Recommandé par les experts en cryptographie
✅ **Intégré** : Disponible nativement dans PHP 7.2+

## 🏗️ Architecture

### Services et composants

```
┌─────────────────────────────────────────────────────────┐
│ src/Service/EncryptionService.php                       │
│ - Gère le chiffrement/déchiffrement avec Sodium         │
└─────────────────────────────────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│ src/EventListener/EmployeeEncryptionListener.php        │
│ - Chiffre automatiquement avant save (prePersist)       │
│ - Chiffre automatiquement avant update (preUpdate)      │
│ - Déchiffre automatiquement après load (postLoad)       │
└─────────────────────────────────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│ Base de données                                          │
│ - google_authenticator_secret: LONGTEXT                 │
│ - Stocke le secret chiffré en base64                    │
└─────────────────────────────────────────────────────────┘
```

## 🔑 Gestion des clés

### Clé de chiffrement

La clé est dérivée du `APP_SECRET` défini dans `.env.local` :

```php
// Dans EncryptionService.php
$this->key = hash('sha256', $appSecret, true); // 32 octets
```

**⚠️ CRITIQUE** : Le `APP_SECRET` doit être sauvegardé ! Sans lui, impossible de déchiffrer.

```env
# .env.local
APP_SECRET=283a2d252254a6728325f8dbd78c2cf36ddd39815566e657a8981efe63af2974
```

### Nonce (Number used ONCE)

Un nonce aléatoire unique est généré pour **chaque chiffrement** :

```php
$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); // 24 octets
```

Le nonce est stocké avec le texte chiffré : `[nonce(24 bytes)][ciphertext(variable)]`

## 📝 Format de stockage

### Secret en clair (Base32)
```
3U2QEP7SFGGO5R3LXJLOD6ISFCIV5MGF2VATF7QBZOZBOJJFN5TA
Longueur : 52 caractères
Format : Base32 (A-Z, 2-7)
```

### Secret chiffré (Base64)
```
c+sad5dhBjLgaaFZDrDfhJ9OVQUSkP9IpNHywPw2CzUSfRxO+7kH8...
Longueur : ~124 caractères
Format : Base64 (A-Za-z0-9+/=)
Structure : [nonce(24o)][ciphertext] encodé en base64
```

## 🔄 Flux de données

### Chiffrement (Sauvegarde)

```php
1. Application veut sauvegarder un Employee
   ↓
2. Listener détecte prePersist/preUpdate
   ↓
3. Vérifie si le secret est en clair (regex Base32)
   ↓
4. Si oui → appel EncryptionService::encrypt()
   ├─ Génère nonce aléatoire (24 octets)
   ├─ Chiffre avec sodium_crypto_secretbox()
   └─ Encode en base64
   ↓
5. Secret chiffré stocké en BDD
```

### Déchiffrement (Lecture)

```php
1. Application charge un Employee depuis la BDD
   ↓
2. Listener détecte postLoad
   ↓
3. Vérifie si le secret est chiffré (regex != Base32)
   ↓
4. Si oui → appel EncryptionService::decrypt()
   ├─ Décode le base64
   ├─ Extrait nonce (24 premiers octets)
   ├─ Extrait ciphertext (reste)
   └─ Déchiffre avec sodium_crypto_secretbox_open()
   ↓
5. Secret en clair en mémoire (jamais en BDD)
```

## 🛠️ Utilisation

### Chiffrement automatique

Aucun code à modifier ! Le chiffrement/déchiffrement est **transparent** :

```php
// Dans un contrôleur
$employee = new Employee();
$employee->setGoogleAuthenticatorSecret('3U2QEP7SFGGO...'); // En clair

$entityManager->persist($employee);
$entityManager->flush(); // ✓ Chiffré automatiquement avant save

// Plus tard...
$employee = $repository->find($id);
$secret = $employee->getGoogleAuthenticatorSecret(); // ✓ Déchiffré automatiquement
// $secret = '3U2QEP7SFGGO...' (en clair)
```

### Migration des secrets existants

Pour chiffrer les secrets déjà en base :

```bash
# Test (dry-run)
php bin/console app:encrypt-secrets --dry-run

# Chiffrement réel
php bin/console app:encrypt-secrets
```

### Test du déchiffrement

```bash
php bin/console app:test-decryption
```

## 🔧 Configuration

### services.yaml

```yaml
App\Service\EncryptionService:
    arguments:
        $appSecret: '%env(APP_SECRET)%'
```

### Entity Employee

```php
#[ORM\Column(type: Types::TEXT, nullable: true)] 
private ?string $googleAuthenticatorSecret = null;
```

**Note** : Type `TEXT` (LONGTEXT) car les secrets chiffrés sont plus longs (~124 caractères vs 52).

## 🔒 Sécurité

### Points forts

✅ **Chiffrement authentifié** : Poly1305 détecte toute modification
✅ **Nonce unique** : Chaque secret a son propre nonce aléatoire
✅ **Clé dérivée** : Utilise SHA-256 pour dériver la clé
✅ **Transparent** : Pas de risque d'oublier de chiffrer
✅ **Détection intelligente** : Distingue Base32 (clair) vs Base64 (chiffré)

### Bonnes pratiques

✅ **Sauvegarder APP_SECRET** : L'imprimer et le stocker en lieu sûr
✅ **Ne jamais commiter .env.local** : Déjà dans .gitignore
✅ **Rotation des secrets** : En cas de compromission, regénérer APP_SECRET et re-chiffrer
✅ **Backups chiffrés** : Les backups de BDD contiennent des secrets chiffrés

### Rotation de clé (si compromission)

```bash
# 1. Générer nouveau APP_SECRET
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"

# 2. Mettre à jour .env.local avec la nouvelle clé

# 3. Déchiffrer avec ancienne clé, re-chiffrer avec nouvelle
# (Commande à créer si nécessaire)
```

## 📊 Détection du format

### Regex de détection

```php
// Secret NON chiffré (Base32)
preg_match('/^[A-Z2-7]+=*$/', $secret)
// Exemple : 3U2QEP7SFGGO5R3L...

// Secret CHIFFRÉ (Base64)
!preg_match('/^[A-Z2-7]+=*$/', $secret)
// Exemple : c+sad5dhBjLgaaFZ...
```

## 🧪 Tests

### Vérifier l'état en BDD

```bash
php bin/console dbal:run-sql "SELECT id, email, LENGTH(google_authenticator_secret) as len, LEFT(google_authenticator_secret, 20) as preview FROM employee WHERE google_authenticator_secret IS NOT NULL"
```

### Vérifier le déchiffrement

```bash
php bin/console app:test-decryption
```

## 📚 Références

- [Libsodium Documentation](https://libsodium.gitbook.io/doc/)
- [PHP Sodium Extension](https://www.php.net/manual/en/book.sodium.php)
- [XSalsa20 Stream Cipher](https://en.wikipedia.org/wiki/Salsa20)
- [Poly1305 MAC](https://en.wikipedia.org/wiki/Poly1305)

## 🐛 Résolution de problèmes

### Extension Sodium non chargée

```ini
# Dans php.ini (C:\xampp\php\php.ini)
extension=sodium

# Redémarrer Apache
```

### Erreur de déchiffrement

1. Vérifier que `APP_SECRET` n'a pas changé
2. Vérifier que le secret en BDD est bien en base64
3. Vérifier les logs d'erreur

### Secret reste en clair

1. Vérifier que le listener est bien chargé
2. Clear le cache : `php bin/console cache:clear`
3. Relancer la migration : `php bin/console app:encrypt-secrets`

## 📝 Fichiers modifiés

- `src/Service/EncryptionService.php` - Service de chiffrement
- `src/EventListener/EmployeeEncryptionListener.php` - Listener Doctrine
- `src/Entity/Employee.php` - Type TEXT pour la colonne
- `src/Command/EncryptSecretsCommand.php` - Migration des secrets
- `src/Command/TestDecryptionCommand.php` - Test du déchiffrement
- `config/services.yaml` - Configuration du service
- `migrations/Version20260215121415.php` - Migration BDD

---

**Date de mise en place** : 15 février 2026  
**Clé à sauvegarder** : `283a2d252254a6728325f8dbd78c2cf36ddd39815566e657a8981efe63af2974`

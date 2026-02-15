# Documentation - Système d'Authentification à Deux Facteurs (2FA)

## 📚 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture globale](#architecture-globale)
3. [Différence : EventSubscriber vs EventListener vs Command](#différence--eventsubscriber-vs-eventlistener-vs-command)
4. [Composants du système 2FA](#composants-du-système-2fa)
5. [Flux d'authentification](#flux-dauthentification)
6. [Configuration](#configuration)
7. [Utilisation](#utilisation)

---

## 📋 Vue d'ensemble

Le système 2FA (Two-Factor Authentication) de TaskLinker est **optionnel** et utilise **Google Authenticator** (codes TOTP). Il est basé sur le bundle **SchebTwoFactorBundle**.

### Caractéristiques

✅ **Optionnel** : L'utilisateur peut choisir d'activer ou non la 2FA  
✅ **Sécurisé** : Secrets chiffrés en base de données avec Sodium  
✅ **Flexible** : Mode bypass pour le développement  
✅ **Convivial** : Popup de suggestion non intrusive  
✅ **Standard** : Compatible avec Google Authenticator, Authy, etc.

---

## 🏗️ Architecture globale

```
┌─────────────────────────────────────────────────────────────────┐
│                     AUTHENTIFICATION                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
        ┌─────────────────────────────────────────────┐
        │  SecurityController::login                  │
        │  - Formulaire login/password                │
        └─────────────────────────────────────────────┘
                              │
                              ↓ (succès)
        ┌─────────────────────────────────────────────┐
        │  EventListener/LoginSuccessListener         │
        │  - Vérifie si 2FA activée                   │
        │  - Gère le bypass dev                       │
        └─────────────────────────────────────────────┘
                              │
                ┌─────────────┴──────────────┐
                │                            │
          2FA activée ?                2FA non activée
                │                            │
                ↓                            ↓
    ┌───────────────────────┐    ┌──────────────────────┐
    │ Redirect → /2fa       │    │ Redirect → /project  │
    │ (vérification code)   │    │ (connexion OK)       │
    └───────────────────────┘    └──────────────────────┘
                │                            │
                ↓                            ↓
    ┌───────────────────────┐    ┌──────────────────────┐
    │ TwoFactorController   │    │ Popup suggestion 2FA │
    │ Code validé ?         │    │ (si non cachée)      │
    └───────────────────────┘    └──────────────────────┘
                │
                ↓ (OK)
    ┌───────────────────────────────────┐
    │ TwoFactorAuthenticationSuccess    │
    │ Handler → /project                │
    └───────────────────────────────────┘
```

---

## 🔄 Différence : EventSubscriber vs EventListener vs Command

### 📌 **EventListener** (Écouteur d'événements)

**Définition** : Classe qui écoute UN événement spécifique et réagit à celui-ci.

**Utilisation** : Pour des actions simples et ciblées.

**Caractéristiques** :
- S'enregistre via l'attribut `#[AsEventListener]`
- Méthode `__invoke()` appelée automatiquement
- Simple et direct

**Exemple dans TaskLinker** :
```php
// src/EventListener/LoginSuccessListener.php
#[AsEventListener(event: LoginSuccessEvent::class, priority: -10)]
class LoginSuccessListener
{
    public function __invoke(LoginSuccessEvent $event): void
    {
        // Action après connexion réussie
    }
}
```

**Rôle dans la 2FA** : 
- Intercepte le succès de connexion
- Gère le bypass en développement


### 📌 **EventSubscriber** (Abonné aux événements)

**Définition** : Classe qui peut écouter PLUSIEURS événements et définir des priorités.

**Utilisation** : Pour des logiques plus complexes impliquant plusieurs événements.

**Caractéristiques** :
- Implémente `EventSubscriberInterface`
- Méthode `getSubscribedEvents()` retourne un tableau d'événements
- Plus flexible, peut gérer plusieurs événements

**Exemple dans TaskLinker** :
```php
// src/EventSubscriber/AccessDeniedSubscriber.php
class AccessDeniedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onAccessDenied',
        ];
    }
    
    public function onAccessDenied(ExceptionEvent $event): void
    {
        // Gère les accès refusés
    }
}
```

**Rôle dans la 2FA** : 
- Intercepte les erreurs d'accès refusé
- Redirige vers une page appropriée


### 📌 **Command** (Commande console)

**Définition** : Classe exécutable depuis le terminal via `php bin/console`.

**Utilisation** : Pour des tâches administratives, migrations, tests, maintenance.

**Caractéristiques** :
- S'enregistre via l'attribut `#[AsCommand]`
- Méthode `execute()` contient la logique
- Exécution manuelle ou planifiée (cron)

**Exemple dans TaskLinker** :
```php
// src/Command/TwoFactorManageCommand.php
#[AsCommand(name: 'app:2fa:manage')]
class TwoFactorManageCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Gérer la 2FA depuis le terminal
    }
}
```

**Rôle dans la 2FA** : 
- Activer/désactiver la 2FA en ligne de commande
- Utile pour l'administration ou le dépannage

---

### 📊 Tableau comparatif

| Critère | EventListener | EventSubscriber | Command |
|---------|--------------|-----------------|---------|
| **Quand ?** | À l'exécution d'un événement | À l'exécution de plusieurs événements | Manuellement depuis le terminal |
| **Combien d'événements ?** | 1 événement | 1 ou plusieurs événements | N/A (pas d'événements) |
| **Priorité ?** | Via attribut | Via `getSubscribedEvents()` | N/A |
| **Complexité** | Simple | Moyenne | Variable |
| **Cas d'usage** | Réaction simple | Logique multi-événements | Tâches admin/maintenance |

---

## 🧩 Composants du système 2FA

### 📂 Entité (Entity)

| Fichier | Rôle |
|---------|------|
| `src/Entity/Employee.php` | Contient les propriétés 2FA : `googleAuthenticatorSecret`, `isTwoFactorEnabled`, `hide2FAPrompt` |

### 🎮 Contrôleurs (Controllers)

| Fichier | Route | Rôle |
|---------|-------|------|
| `TwoFactorController::setup()` | `/2fa/setup` | Affiche le QR Code et le secret manuel |
| `TwoFactorController::enable()` | `/2fa/enable` | Vérifie le code et active la 2FA |
| `TwoFactorController::hidePrompt()` | `/2fa/hide-prompt` | Cache la popup de suggestion |
| `SecurityController::login()` | `/login` | Formulaire de connexion principal |

### 🎯 Event Listeners

| Fichier | Événement écouté | Rôle |
|---------|------------------|------|
| `LoginSuccessListener.php` | `LoginSuccessEvent` | Intercepte la connexion réussie, gère le bypass dev |
| `EmployeeEncryptionListener.php` | `prePersist`, `preUpdate`, `postLoad` | Chiffre/déchiffre automatiquement les secrets 2FA |

### 📡 Event Subscribers

| Fichier | Événements | Rôle |
|---------|-----------|------|
| `AccessDeniedSubscriber.php` | `ExceptionEvent` | Gère les erreurs d'accès refusé, redirige proprement |

### 🔐 Gestionnaires de sécurité (Security)

| Fichier | Rôle |
|---------|------|
| `TwoFactorAuthenticationSuccessHandler.php` | Gère la redirection après succès 2FA (vers `/project`) |

### 💻 Commandes (Commands)

| Fichier | Commande | Rôle |
|---------|----------|------|
| `TwoFactorManageCommand.php` | `app:2fa:manage` | Active/désactive la 2FA en CLI |
| `EncryptSecretsCommand.php` | `app:encrypt-secrets` | Chiffre les secrets existants en BDD |
| `TestDecryptionCommand.php` | `app:test-decryption` | Teste le déchiffrement automatique |

### 🖼️ Templates (Views)

| Fichier | Route | Rôle |
|---------|-------|------|
| `security/2fa_setup.html.twig` | `/2fa/setup` | Page de configuration avec QR Code |
| `security/2fa_form.html.twig` | `/2fa` | Formulaire de vérification du code 2FA |
| `base.html.twig` | Toutes les pages | Contient la popup de suggestion 2FA |

### ⚙️ Configuration

| Fichier | Rôle |
|---------|------|
| `config/packages/scheb_2fa.yaml` | Configuration du bundle 2FA (serveur, template, etc.) |
| `config/packages/security.yaml` | Règles d'accès et firewall 2FA |
| `config/routes/scheb_2fa.yaml` | Routes `/2fa` et `/2fa_check` |
| `config/services.yaml` | Configuration du service de chiffrement |
| `.env.local` | `BYPASS_2FA=0` pour activer/désactiver en dev |

### 🛠️ Services

| Fichier | Rôle |
|---------|------|
| `EncryptionService.php` | Chiffre/déchiffre les secrets avec Sodium |

---

## 🔄 Flux d'authentification

### Scénario 1 : Utilisateur SANS 2FA

```
1. Utilisateur entre login/password sur /login
   ↓
2. SecurityController valide les identifiants
   ↓
3. LoginSuccessListener (EventListener) détecte le succès
   ↓
4. Vérification : 2FA activée ? → NON
   ↓
5. Redirection vers /project
   ↓
6. base.html.twig affiche la popup de suggestion 2FA
   (sauf si l'utilisateur a cliqué "Ne plus me le demander")
```

### Scénario 2 : Utilisateur AVEC 2FA

```
1. Utilisateur entre login/password sur /login
   ↓
2. SecurityController valide les identifiants
   ↓
3. LoginSuccessListener détecte le succès
   ↓
4. Vérification : 2FA activée ? → OUI
   ↓
5. SchebTwoFactorBundle intercepte → Redirection vers /2fa
   ↓
6. TwoFactorController::form affiche le formulaire de code
   ↓
7. Utilisateur entre le code de Google Authenticator
   ↓
8. Scheb vérifie le code
   ↓
9. TwoFactorAuthenticationSuccessHandler redirige vers /project
```

### Scénario 3 : Configuration de la 2FA

```
1. Utilisateur clique sur "Activer la 2FA" (popup ou menu)
   ↓
2. Redirect vers /2fa/setup
   ↓
3. TwoFactorController::setup
   ├─ Génère un secret (si pas déjà existant)
   ├─ Chiffre le secret (EmployeeEncryptionListener)
   ├─ Génère le QR Code
   └─ Affiche la page 2fa_setup.html.twig
   ↓
4. Utilisateur scanne le QR Code avec Google Authenticator
   ↓
5. Utilisateur clique sur "J'ai scanné le QR Code"
   ↓
6. Redirect vers /2fa/enable
   ↓
7. TwoFactorController::enable affiche le formulaire
   ↓
8. Utilisateur entre le code de son app
   ↓
9. Vérification du code via GoogleAuthenticatorInterface
   ↓
10. Si correct : setIsTwoFactorEnabled(true) + flush
   ↓
11. Flash message + Redirect vers /project
```

---

## ⚙️ Configuration

### Variables d'environnement (.env.local)

```env
# Bypass 2FA en développement (set to 1 to disable 2FA)
BYPASS_2FA=0

# Clé de chiffrement (ne JAMAIS perdre !)
APP_SECRET=283a2d252254a6728325f8dbd78c2cf36ddd39815566e657a8981efe63af2974
```

### Configuration Scheb 2FA (config/packages/scheb_2fa.yaml)

```yaml
scheb_two_factor:
    security_tokens:
        - Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
        - Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken
    google: 
        enabled: true
        server_name: TaskLinker
        issuer: TaskLinker
        digits: 6
        leeway: 0
        template: security/2fa_form.html.twig
```

### Règles de sécurité (config/packages/security.yaml)

```yaml
firewalls:
    main:
        two_factor: 
            auth_form_path: 2fa_login 
            check_path: 2fa_login_check

access_control:
    # Pages de configuration 2FA
    - { path: ^/2fa/setup, roles: IS_AUTHENTICATED }
    - { path: ^/2fa/enable, roles: IS_AUTHENTICATED }
    
    # Page de vérification 2FA (pendant le processus)
    - { path: ^/2fa, role: IS_AUTHENTICATED_2FA_IN_PROGRESS }
    
    # Pages protégées (nécessitent 2FA si activée)
    - { path: ^/, roles: IS_AUTHENTICATED_FULLY }
```

---

## 🎯 Utilisation

### Pour l'utilisateur

#### Activer la 2FA

1. Se connecter à l'application
2. Accepter la popup de suggestion (ou aller dans les paramètres)
3. Scanner le QR Code avec Google Authenticator
4. Entrer le code à 6 chiffres pour confirmer
5. La 2FA est maintenant activée

#### Se connecter avec la 2FA

1. Entrer login/password
2. Entrer le code de Google Authenticator (6 chiffres)
3. Accès autorisé

#### Désactiver la 2FA

Utiliser la commande CLI :
```bash
php bin/console app:2fa:manage user@example.com disable
```

### Pour l'administrateur

#### Bypass 2FA en développement

```env
# .env.local
BYPASS_2FA=1
```

Redémarrer le serveur Symfony.

#### Gérer la 2FA en CLI

```bash
# Désactiver la 2FA pour un utilisateur
php bin/console app:2fa:manage user@example.com disable

# Voir les instructions pour activer
php bin/console app:2fa:manage user@example.com enable
```

#### Chiffrer les secrets existants

```bash
# Test (dry-run)
php bin/console app:encrypt-secrets --dry-run

# Chiffrement réel
php bin/console app:encrypt-secrets
```

#### Tester le déchiffrement

```bash
php bin/console app:test-decryption
```

---

## 🔐 Sécurité

### Chiffrement des secrets

Les secrets Google Authenticator sont **chiffrés en base de données** avec :
- **Algorithme** : Sodium (XSalsa20-Poly1305)
- **Clé** : Dérivée du `APP_SECRET`
- **Automatique** : Via `EmployeeEncryptionListener`

Voir [ENCRYPTION_SODIUM.md](ENCRYPTION_SODIUM.md) pour plus de détails.

### Bonnes pratiques

✅ Sauvegarder le `APP_SECRET`  
✅ Ne jamais commiter `.env.local`  
✅ Utiliser HTTPS en production  
✅ Tester régulièrement la 2FA  
✅ Former les utilisateurs  

---

## 📝 Résumé des fichiers par catégorie

### 🔴 Critiques (à ne pas perdre)
- `.env.local` → `APP_SECRET` (clé de chiffrement)
- `src/Entity/Employee.php` → Propriétés 2FA

### 🟠 Logique métier
- `src/Controller/TwoFactorController.php` → Gestion 2FA
- `src/EventListener/LoginSuccessListener.php` → Post-connexion
- `src/EventListener/EmployeeEncryptionListener.php` → Chiffrement auto
- `src/Security/TwoFactorAuthenticationSuccessHandler.php` → Redirection

### 🟡 Administration
- `src/Command/TwoFactorManageCommand.php` → CLI admin
- `src/Command/EncryptSecretsCommand.php` → Migration sécurité
- `src/EventSubscriber/AccessDeniedSubscriber.php` → Gestion erreurs

### 🟢 Configuration
- `config/packages/scheb_2fa.yaml` → Config bundle
- `config/packages/security.yaml` → Firewall + accès
- `config/routes/scheb_2fa.yaml` → Routes 2FA

### 🔵 Interface utilisateur
- `templates/security/2fa_setup.html.twig` → QR Code
- `templates/security/2fa_form.html.twig` → Vérification code
- `templates/base.html.twig` → Popup suggestion

---

## 🎓 Pour aller plus loin

- [Documentation SchebTwoFactorBundle](https://symfony.com/bundles/SchebTwoFactorBundle/current/index.html)
- [Documentation Sodium](ENCRYPTION_SODIUM.md)
- [RFC 6238 - TOTP Algorithm](https://tools.ietf.org/html/rfc6238)

---

**Dernière mise à jour** : 15 février 2026  
**Version** : TaskLinker 1.0

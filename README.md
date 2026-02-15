# TaskLinker

Application de gestion de tâches et de projets développée avec Symfony 7, incluant un système d'authentification à deux facteurs (2FA) et un contrôle d'accès granulaire par rôles.

## Description

TaskLinker est une application web sécurisée permettant de gérer des projets, des tâches et des employés. Elle offre une interface intuitive type Kanban pour créer, modifier et suivre l'avancement des tâches assignées aux différents employés d'une organisation.

## Fonctionnalités principales

### Gestion de projets
- **Création et organisation** : Créer des projets et assigner des membres
- **Vue Kanban** : Visualisation des tâches en colonnes (To Do, Doing, Done)
- **Archivage** : Archiver les projets terminés
- **Contrôle d'accès** : Seuls les membres assignés voient leurs projets

### Gestion des tâches
- **Création et assignation** : Créer des tâches avec deadline et description
- **Statuts** : Suivi de l'état d'avancement (TODO, DOING, DONE)
- **Permissions** : 
  - ROLE_ADMIN peut tout modifier et supprimer
  - ROLE_USER peut modifier uniquement ses tâches assignées (pas de suppression)

### Gestion des employés
- **Statuts** : Active, En congés, Malade, Formation, Démission, Licenciement
- **Rôles** : ROLE_ADMIN (chef de projet) ou ROLE_USER (collaborateur)
- **Profils** : Informations détaillées (prénom, nom, email, date d'entrée)

### Sécurité avancée
- **Authentification à deux facteurs (2FA)** : Google Authenticator obligatoire
- **Chiffrement** : Secrets 2FA chiffrés avec Sodium (XSalsa20-Poly1305)
- **Contrôle d'accès** : Système de Voters Symfony pour permissions granulaires
- **Protection CSRF** : Sur toutes les actions de modification

## Prérequis

- PHP 8.2+ avec extensions :
  - `pdo_mysql`
  - `sodium` (pour le chiffrement)
  - `intl`
- Composer
- MySQL/MariaDB 10.4+
- XAMPP (ou environnement équivalent)
- Symfony CLI (optionnel mais recommandé)

## Installation

### 1. Cloner le projet
```bash
cd c:\xampp\htdocs
git clone [url-du-repo] TaskLinker
cd TaskLinker
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer l'environnement

Créez un fichier `.env.local` à la racine du projet :
```env
# Configuration de la base de données
DATABASE_URL="mysql://root:@127.0.0.1:3306/tasklinker_db?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

# Clé secrète pour le chiffrement (IMPORTANT : Générer une nouvelle clé)
APP_SECRET=votre_cle_secrete_32_caracteres_minimum
```

**⚠️ IMPORTANT** : Générez une nouvelle `APP_SECRET` unique pour votre installation :
```bash
php -r "echo bin2hex(random_bytes(32));"
```

### 4. Créer la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de test (optionnel)
```bash
php bin/console doctrine:fixtures:load
```

### 6. Activer l'extension Sodium

Vérifiez que Sodium est activé dans votre `php.ini` :
```ini
extension=sodium
```

Pour vérifier :
```bash
php -r "echo (extension_loaded('sodium') ? 'Sodium est activé' : 'Sodium n\'est pas activé');"
```

### 7. Démarrer le serveur

Avec Symfony CLI (recommandé) :
```bash
symfony server:start
```

Ou avec le serveur PHP intégré :
```bash
php -S localhost:8000 -t public
```

## Utilisation

### Accès à l'application
- URL locale : `http://localhost:8000` (Symfony CLI)
- XAMPP : `http://localhost/TaskLinker/public`

### Comptes de démonstration

Après avoir chargé les fixtures :
- **Admin** : admin@example.com / password
- **Utilisateur** : user@example.com / password

### Première connexion

1. Connectez-vous avec vos identifiants
2. **Configurez la 2FA** :
   - Une popup apparaîtra vous invitant à activer la 2FA
   - Scannez le QR code avec Google Authenticator
   - Entrez le code à 6 chiffres pour valider
3. À la prochaine connexion, entrez votre code 2FA

### Rôles et permissions

#### ROLE_ADMIN (Chef de projet)
- ✅ Créer, modifier, supprimer des projets
- ✅ Créer, modifier, supprimer toutes les tâches
- ✅ Gérer tous les employés
- ✅ Assigner des membres aux projets
- ✅ Accès complet sans restriction

#### ROLE_USER (Collaborateur)
- ✅ Voir les projets auxquels il est assigné
- ✅ Voir toutes les tâches de ses projets
- ✅ Modifier uniquement ses tâches assignées
- ❌ Ne peut pas supprimer de tâches
- ❌ Ne peut pas créer/modifier/supprimer de projets
- ❌ Ne peut pas gérer les employés

## Structure du projet

```
TaskLinker/
├── assets/                    # Frontend (AssetMapper)
│   ├── app.js                # Point d'entrée JavaScript
│   ├── controllers/          # Contrôleurs Stimulus
│   │   └── csrf_protection_controller.js
│   ├── js/
│   │   └── select.js         # Initialisation Select2
│   └── styles/
│       └── app.css           # Styles principaux
├── config/
│   ├── packages/
│   │   ├── security.yaml     # Configuration 2FA et accès
│   │   ├── doctrine.yaml
│   │   ├── scheb_2fa.yaml   # Configuration 2FA
│   │   └── ...
│   ├── routes.yaml
│   └── services.yaml         # Services (EncryptionService)
├── docs/                      # Documentation technique
│   ├── README.md             # Index de la documentation
│   ├── TWO_FACTOR_AUTHENTICATION.md
│   ├── ENCRYPTION_SODIUM.md
│   ├── PERMISSIONS.md
│   └── AUDIT_DEPENDENCIES.md
├── migrations/                # Migrations Doctrine
├── public/
│   └── index.php             # Point d'entrée
├── src/
│   ├── Command/              # Commandes CLI
│   │   ├── TwoFactorManageCommand.php
│   │   ├── EncryptSecretsCommand.php
│   │   └── TestDecryptionCommand.php
│   ├── Controller/
│   │   ├── EmployeeController.php
│   │   ├── ProjectController.php
│   │   ├── TaskController.php
│   │   ├── SecurityController.php
│   │   └── TwoFactorController.php
│   ├── Entity/
│   │   ├── Employee.php      # Utilisateurs avec 2FA
│   │   ├── Project.php
│   │   └── Task.php
│   ├── Enum/
│   │   ├── EmployeeStatus.php
│   │   └── TaskStatus.php
│   ├── EventListener/
│   │   └── EmployeeEncryptionListener.php  # Chiffrement auto
│   ├── Form/
│   │   ├── EmployeeType.php
│   │   ├── ProjectType.php
│   │   └── TaskType.php
│   ├── Repository/
│   ├── Security/
│   │   ├── TwoFactorAuthenticationSuccessHandler.php
│   │   └── Voter/           # Système de permissions
│   │       ├── ProjectAccessVoter.php
│   │       └── TaskVoter.php
│   └── Service/
│       └── EncryptionService.php  # Sodium encryption
├── templates/
│   ├── base.html.twig         # Layout principal + popup 2FA
│   ├── employee/
│   ├── project/
│   │   └── view.html.twig    # Vue Kanban
│   ├── task/
│   └── security/
└── tests/
```

## Technologies utilisées

- **Framework** : Symfony 7.4
- **PHP** : 8.2.12
- **Base de données** : Doctrine ORM avec MySQL/MariaDB 10.4.32
- **Templates** : Twig 3.x
- **Sécurité** :
  - Scheb 2FA Bundle 7.13 (Google Authenticator)
  - Sodium (chiffrement XSalsa20-Poly1305)
- **Frontend** :
  - AssetMapper (Symfony 7)
  - Stimulus 2.31
  - Turbo 2.31 (Hotwired)
  - jQuery 3.7.1
  - Select2 4.1.0
  - FontAwesome 6.2.1
- **Tests** : PHPUnit 11
- **Fixtures** : Zenstruck Foundry 2.2

## Commandes CLI disponibles

### Gestion de la 2FA
```bash
# Activer/désactiver la 2FA pour un utilisateur
php bin/console app:2fa:manage user@example.com --enable
php bin/console app:2fa:manage user@example.com --disable

# Afficher le statut 2FA
php bin/console app:2fa:manage user@example.com --status
```

### Gestion du chiffrement
```bash
# Chiffrer les secrets en clair (migration)
php bin/console app:encrypt-secrets

# Tester le déchiffrement d'un secret
php bin/console app:test-decryption 62
```

### Base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load
```

## Documentation technique

Une documentation  est disponible dans le dossier `docs/` :

- **[docs/TWO_FACTOR_AUTHENTICATION.md](docs/TWO_FACTOR_AUTHENTICATION.md)** - Guide 2FA complet
- **[docs/ENCRYPTION_SODIUM.md](docs/ENCRYPTION_SODIUM.md)** - Architecture du chiffrement avec Sodium
## Sécurité

### Chiffrement des secrets 2FA

Les secrets Google Authenticator sont automatiquement chiffrés :
- **Algorithme** : Sodium XSalsa20-Poly1305 (AEAD)
- **Clé** : Dérivée de `APP_SECRET` (32 bytes)
- **Stockage** : Base64 en base de données
- **Chiffrement auto** : Via `EmployeeEncryptionListener`

### Configuration 2FA

La 2FA est **obligatoire** pour tous les utilisateurs :
- Popup automatique au premier login
- Accès aux routes bloqué sans 2FA validée
- Routes de configuration accessibles sans 2FA pour activation initiale

### Permissions

Le système utilise des **Voters Symfony** pour contrôler l'accès :
- `ProjectAccessVoter` : Pour les projets (VIEW, EDIT)
- `TaskVoter` : Pour les tâches (VIEW, EDIT, DELETE)

## Développement

### Tests
```bash
# Exécuter tous les tests
php bin/phpunit

# Tests avec couverture
php bin/phpunit --coverage-html coverage
```

### Debug
```bash
# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router

# Debug une entité
php bin/console doctrine:mapping:info
```

### AssetMapper
```bash
# Compiler les assets pour la production
php bin/console asset-map:compile
```

## Déploiement

### Préparer pour la production

1. **Variables d'environnement** (`.env.local`) :
```env
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL="mysql://user:password@host:3306/database"
APP_SECRET=[nouvelle_cle_secrete_unique]
```

2. **Compiler les assets** :
```bash
php bin/console asset-map:compile
```

3. **Vider et chauffer le cache** :
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

4. **Migrer la base de données** :
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

5. **Permissions** :
```bash
chmod -R 777 var/cache var/log
```

## Dépannage

### La 2FA ne fonctionne pas
- Vérifier que l'horloge du serveur est synchronisée
- Vérifier que Sodium est activé (`php -m | grep sodium`)
- Consulter les logs : `var/log/dev.log`

### Erreur "Token CSRF invalide"
- Vider le cache : `php bin/console cache:clear`
- Vérifier que les sessions fonctionnent correctement

### Select2 ne s'initialise pas
- Vérifier la console JavaScript (F12)
- Vérifier que jQuery est chargé avant Select2
- Consulter : `assets/js/select.js`

## Contributeurs

Développé avec Symfony 7 et les meilleures pratiques de sécurité.

## Licence

Projet éducatif - Tous droits réservés.

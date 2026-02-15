# 📚 Documentation TaskLinker

Bienvenue dans la documentation technique de TaskLinker.

## 📑 Index des documents

### 🔐 Sécurité & Authentification

- **[TWO_FACTOR_AUTHENTICATION.md](TWO_FACTOR_AUTHENTICATION.md)**  
  Guide complet du système d'authentification à deux facteurs (2FA)
  - Architecture globale
  - Différence EventSubscriber vs EventListener vs Command
  - Flux d'authentification
  - Configuration et utilisation
  
- **[ENCRYPTION_SODIUM.md](ENCRYPTION_SODIUM.md)**  
  Documentation technique du chiffrement des secrets 2FA avec Sodium
  - Algorithme XSalsa20-Poly1305
  - Architecture du système de chiffrement
  - Gestion des clés et nonces
  - Migration et maintenance

- **[PERMISSIONS.md](PERMISSIONS.md)**  
  Documentation complète du système de permissions et d'autorisation
  - Rôles et leurs capacités (ADMIN, CHEF_PROJET, COLLABORATOR)
  - Permissions par entité (Projets et Tâches)
  - Implémentation avec les Voters Symfony
  - Cas d'usage et exemples

### 📊 Gestion de Projet

- **[AUDIT_DEPENDENCIES.md](AUDIT_DEPENDENCIES.md)**  
  Audit complet des dépendances et ressources
  - Dépendances Composer
  - Scripts et assets frontend
  - Recommandations d'optimisation

## 🗂️ Structure des dossiers

```
src/
├── Command/              # Commandes CLI (maintenance, admin)
│   ├── TwoFactorManageCommand.php
│   ├── EncryptSecretsCommand.php
│   └── TestDecryptionCommand.php
│
├── Controller/           # Contrôleurs (logique métier)
│   ├── TwoFactorController.php
│   ├── SecurityController.php
│   ├── ProjectController.php
│   ├── TaskController.php
│   └── ...
│
├── Entity/              # Entités Doctrine (modèles de données)
│   ├── Employee.php
│   ├── Project.php
│   └── Task.php
│
├── EventListener/       # Écouteurs d'événements (1 événement)
│   ├── LoginSuccessListener.php
│   └── EmployeeEncryptionListener.php
│
├── EventSubscriber/     # Abonnés aux événements (plusieurs événements)
│   └── AccessDeniedSubscriber.php
│
├── Security/            # Gestionnaires de sécurité et autorisation
│   ├── TwoFactorAuthenticationSuccessHandler.php
│   └── Voter/          # Symfony Voters pour les permissions
│       ├── ProjectAccessVoter.php
│       └── TaskVoter.php
│
└── Service/             # Services réutilisables
    └── EncryptionService.php
```

## 🔧 Différences clés

### Command vs Listener vs Subscriber

| Type | Quand ? | Combien ? | Usage |
|------|---------|-----------|-------|
| **Command** | Manuellement (CLI) | N/A | Tâches admin/maintenance |
| **EventListener** | À l'événement | 1 événement | Action simple et ciblée |
| **EventSubscriber** | À l'événement | 1+ événements | Logique multi-événements |

**Exemple** :
- `TwoFactorManageCommand` → Gérer la 2FA en CLI
- `LoginSuccessListener` → Réagir à la connexion réussie
- `AccessDeniedSubscriber` → Gérer plusieurs types d'erreurs d'accès

## 🚀 Démarrage rapide

### Configuration 2FA

```bash
# Activer Sodium dans php.ini
extension=sodium

# Migrer la BDD
php bin/console doctrine:migrations:migrate

# Chiffrer les secrets existants
php bin/console app:encrypt-secrets
```

### Variables d'environnement

```env
# .env.local
APP_SECRET=votre_cle_secrete_ici
BYPASS_2FA=0  # 1 pour désactiver en dev
```

### Commandes utiles

```bash
# Gérer la 2FA
php bin/console app:2fa:manage user@example.com disable

# Chiffrer les secrets
php bin/console app:encrypt-secrets --dry-run

# Tester le déchiffrement
php bin/console app:test-decryption
```

## 📖 Lectures recommandées

1. Commencez par [TWO_FACTOR_AUTHENTICATION.md](TWO_FACTOR_AUTHENTICATION.md) pour comprendre l'architecture
2. Consultez [ENCRYPTION_SODIUM.md](ENCRYPTION_SODIUM.md) pour la sécurité
3. Explorez le code source commenté

## ⚠️ Important

**À ne jamais perdre** :
- `APP_SECRET` (stocké dans `.env.local`)
- Backups de la base de données

**À ne jamais commiter** :
- `.env.local`
- Clés privées
- Secrets de production

## 🆘 Aide

En cas de problème :
1. Consulter la section "Résolution de problèmes" des docs
2. Vérifier les logs : `var/log/dev.log`
3. Tester avec `BYPASS_2FA=1` en développement

---

**Dernière mise à jour** : 15 février 2026  
**Mainteneur** : Équipe TaskLinker

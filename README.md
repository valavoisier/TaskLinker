# TaskLinker

Application de gestion de tâches et de projets développée avec Symfony.

## Description

TaskLinker est une application web permettant de gérer des projets, des tâches et des employés. Elle offre une interface intuitive pour créer, modifier et suivre l'avancement des tâches assignées aux différents employés d'une organisation.

## Fonctionnalités

- **Gestion des employés** : Créer et gérer les employés avec leur statut
- **Gestion des projets** : Créer et organiser des projets
- **Gestion des tâches** : Créer, assigner et suivre des tâches avec des deadlines
- **Statuts de tâches** : Suivi de l'état d'avancement des tâches
## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL/MariaDB
- XAMPP

## Installation

### Avec XAMPP

1. **Cloner le projet**
   ```bash
   cd c:\xampp\htdocs
   git clone [url-du-repo] TaskLinker
   cd TaskLinker
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer la base de données**
   
   Créez un fichier `.env.local` à la racine du projet et configurez votre connexion :
   ```
   DATABASE_URL="mysql://root:@127.0.0.1:3306/tasklinker?serverVersion=8.0"
   ```

4. **Créer la base de données**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Charger les données de test (optionnel)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

6. **Démarrer le serveur**
   ```bash
   symfony server:start
   ```
   
   Ou utilisez le serveur intégré de PHP :
   ```bash
   php -S localhost:8000 -t public
   ```

## Utilisation

Accédez à l'application via votre navigateur :
- URL locale : `http://localhost:8000`
- XAMPP : `http://localhost/TaskLinker/public`

### Pages principales

- `/` - Page d'accueil
- `/employee` - Liste des employés
- `/project` - Liste et gestion des projets
- `/task` - Création et gestion des tâches

## Structure du projet

```
TaskLinker/
├── assets/              # Fichiers JavaScript et CSS
│   ├── controllers/     # Contrôleurs Stimulus
│   ├── styles/          # Styles CSS
│   └── js/             # Scripts JavaScript
├── config/              # Configuration Symfony
├── migrations/          # Migrations de base de données
├── public/              # Point d'entrée public
├── src/
│   ├── Controller/      # Contrôleurs
│   ├── Entity/          # Entités Doctrine
│   ├── Enum/           # Énumérations (statuts)
│   ├── Factory/         # Factories pour les fixtures
│   ├── Form/           # Types de formulaires
│   ├── Repository/     # Repositories Doctrine
│   └── Story/          # Stories pour les fixtures
├── templates/           # Templates Twig
└── tests/              # Tests unitaires et fonctionnels
```

## Technologies utilisées

- **Framework** : Symfony 7.x
- **Base de données** : Doctrine ORM avec MySQL
- **Templates** : Twig
- **Frontend** : Stimulus, Turbo (Hotwired)
- **Tests** : PHPUnit
- **Fixtures** : Zenstruck Foundry
- **Assets** : AssetMapper

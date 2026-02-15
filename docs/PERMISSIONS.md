# Système de permissions TaskLinker

## Vue d'ensemble

Le système de permissions utilise des **Voters** Symfony pour contrôler l'accès aux projets et tâches selon le rôle de l'utilisateur.

## Rôles disponibles

- **ROLE_ADMIN** : Accès total à toutes les fonctionnalités
- **ROLE_CHEF_PROJET** : Gestion complète des projets assignés et de leurs tâches
- **ROLE_COLLABORATOR** : Accès limité aux projets assignés et à ses propres tâches

## Permissions par entité

### Projets (ProjectAccessVoter)

| Permission | Description | ADMIN | CHEF_PROJET | COLLABORATOR |
|------------|-------------|-------|-------------|--------------|
| PROJECT_VIEW | Voir les détails d'un projet | ✅ Tous les projets | ✅ Si assigné au projet | ✅ Si assigné au projet |
| PROJECT_EDIT | Modifier/créer/archiver/supprimer un projet | ✅ Tous les projets | ❌ | ❌ |

**Règles d'accès :**
- Un utilisateur doit être assigné au projet pour y accéder (sauf ADMIN)
- Seuls les ADMIN peuvent créer, modifier, archiver ou supprimer des projets
- Les CHEF_PROJET et COLLABORATOR peuvent uniquement voir les projets où ils sont assignés

### Tâches (TaskVoter)

| Permission | Description | ADMIN | CHEF_PROJET | COLLABORATOR |
|------------|-------------|-------|-------------|--------------|
| TASK_VIEW | Voir les tâches d'un projet | ✅ Toutes les tâches | ✅ Si assigné au projet | ✅ Si assigné au projet |
| TASK_EDIT | Modifier une tâche | ✅ Toutes les tâches | ✅ Toutes les tâches du projet | ✅ Uniquement ses propres tâches |
| TASK_DELETE | Supprimer une tâche | ✅ Toutes les tâches | ✅ Toutes les tâches du projet | ❌ |

**Règles d'accès :**
- Un utilisateur doit être membre du projet pour voir ses tâches
- Les COLLABORATOR ne peuvent modifier que les tâches qui leur sont assignées (`task.employee === user`)
- Les COLLABORATOR ne peuvent jamais supprimer de tâches
- Les CHEF_PROJET peuvent gérer toutes les tâches des projets où ils sont assignés

## Cas d'usage par rôle

### ADMIN
- Créer, modifier et supprimer n'importe quel projet
- Assigner des employés aux projets
- Voir, modifier et supprimer toutes les tâches de tous les projets
- Accès complet sans restriction

### CHEF_PROJET
- Voir uniquement les projets où il est assigné
- Créer des tâches dans ses projets
- Modifier toutes les tâches de ses projets
- Supprimer toutes les tâches de ses projets
- Changer le statut de toutes les tâches
- Ne peut PAS modifier/supprimer/archiver le projet lui-même

### COLLABORATOR
- Voir uniquement les projets où il est assigné
- Voir toutes les tâches des projets assignés
- Modifier uniquement les tâches qui lui sont assignées
- Changer le statut de ses tâches
- Ne peut PAS créer de tâches
- Ne peut PAS supprimer de tâches
- Ne peut PAS modifier les tâches des autres
- Ne peut PAS modifier/supprimer/archiver le projet

## Implémentation technique

### Voters

1. **ProjectAccessVoter** (`src/Security/Voter/ProjectAccessVoter.php`)
   - Gère les permissions PROJECT_VIEW et PROJECT_EDIT
   - Vérifie l'appartenance au projet via `$project->getEmployees()->contains($user)`

2. **TaskVoter** (`src/Security/Voter/TaskVoter.php`)
   - Gère les permissions TASK_VIEW, TASK_EDIT et TASK_DELETE
   - Vérifie l'appartenance au projet ET l'assignation de la tâche pour TASK_EDIT

### Utilisation dans les contrôleurs

```php
// Vérifier l'accès à un projet
$this->denyAccessUnlessGranted('PROJECT_VIEW', $project);
$this->denyAccessUnlessGranted('PROJECT_EDIT', $project);

// Vérifier l'accès à une tâche
$this->denyAccessUnlessGranted('TASK_EDIT', $task);
$this->denyAccessUnlessGranted('TASK_DELETE', $task);
```

### Utilisation dans les templates Twig

```twig
{# Vérifier si l'utilisateur peut modifier un projet #}
{% if is_granted('PROJECT_EDIT', project) %}
    <a href="{{ path('project_edit', {id: project.id}) }}">Modifier</a>
{% endif %}

{# Vérifier si l'utilisateur peut modifier une tâche #}
{% if is_granted('TASK_EDIT', task) %}
    <a href="{{ path('task_edit', {id: task.id}) }}">Modifier</a>
{% endif %}

{# Vérifier si l'utilisateur peut supprimer une tâche #}
{% if is_granted('TASK_DELETE', task) %}
    <button>Supprimer</button>
{% endif %}
```

## Sécurité

### Protection au niveau contrôleur
Toutes les routes sensibles sont protégées par `denyAccessUnlessGranted()` qui lance une exception `AccessDeniedException` (HTTP 403) si l'accès est refusé.

### Protection au niveau template
Les boutons et liens vers les actions non autorisées sont masqués automatiquement grâce aux conditions `is_granted()`.

### Protection CSRF
Toutes les actions de modification (POST) sont protégées par des tokens CSRF :
```php
$this->isCsrfTokenValid('delete_task_' . $task->getId(), $token)
```

## Tests de permissions

Pour tester les permissions, connectez-vous avec différents rôles et vérifiez :

1. **ADMIN** : Peut tout faire
2. **CHEF_PROJET assigné** : Peut gérer les tâches, mais pas modifier le projet
3. **COLLABORATOR assigné** : Ne voit que ses tâches comme liens clickables
4. **Utilisateur non assigné** : Reçoit une erreur 403 en tentant d'accéder au projet

## Logs et debug

En cas d'erreur d'accès, Symfony log l'événement dans `var/log/dev.log` :
```
Access denied for user [email] on [permission] for [entity]
```

Pour vérifier les permissions en debug :
```php
dump($this->isGranted('TASK_EDIT', $task));
```

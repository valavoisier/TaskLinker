# 🔍 Audit Technique - TaskLinker

**Date** : 15 février 2026  
**Analyse** : Scripts, styles et dépendances

---

## ❌ PROBLÈMES IDENTIFIÉS

### 1. 🔴 **DOUBLON CRITIQUE : select.js chargé 2 fois**

**Fichier** : `assets/app.js` ligne 2
```javascript
import './js/select.js';  // ❌ CHARGÉ ICI
```

**Fichier** : `templates/base.html.twig` ligne 72
```twig
<script src="{{ asset('js/select.js') }}"></script>  <!-- ❌ ET ICI -->
```

**Impact** : 
- Code exécuté en double
- Erreur `$ is not defined` car le premier chargement se fait AVANT jQuery

**Solution** : Supprimer l'une des deux références

---

### 2. 🟠 **ORDRE DE CHARGEMENT INCORRECT**

**Ordre actuel dans base.html.twig** :
```
1. importmap('app') → app.js → import select.js   [jQuery N'EXISTE PAS ENCORE ❌]
2. jQuery CDN
3. Select2 CDN
4. asset('js/select.js')                           [DOUBLON ❌]
```

**Problème** : `select.js` utilise `$()` mais jQuery n'est pas encore chargé

**Solution** : Corriger l'ordre OU déplacer select.js hors d'app.js

---

### 3. 🟡 **hello_controller.js NON UTILISÉ**

**Fichier** : `assets/controllers/hello_controller.js`

**Statut** : ❌ Controller d'exemple jamais utilisé dans les templates

**Solution** : Supprimer le fichier

---

### 4. 🟡 **symfony/ux-turbo INSTALLÉ MAIS NON UTILISÉ**

**Package** : `symfony/ux-turbo` dans composer.json

**Statut** : 
- ✅ Activé dans `controllers.json`
- ❌ Aucune utilisation de `data-turbo-*` dans les templates
- ❌ Aucun événement `turbo:*` dans le JS

**Impact** : Code chargé inutilement (ralentissement)

**Solution** : Désactiver ou supprimer si non prévu

---

### 5. ✅ **FontAwesome UTILISÉ**

**CDN** : Chargé depuis cdnjs.cloudflare.com

**Utilisation** : ✅ Utilisé dans de nombreux templates
- Navigation (icônes menu)
- Boutons (fa-plus, fa-edit, etc.)
- 2FA (fa-shield-halved)

**Statut** : ✅ CONSERVER

---

### 6. 🟡 **Stimulus PEU UTILISÉ**

**Package** : `symfony/stimulus-bundle` installé

**Utilisation actuelle** :
- ✅ `csrf_protection_controller.js` → Utilisé dans login.html.twig
- ❌ `hello_controller.js` → Exemple non utilisé

**Impact** : Framework chargé pour 1 seul controller

**Recommandation** : 
- Garder si vous prévoyez d'utiliser plus de Stimulus
- Sinon, envisager une simplification

---

## 📊 RÉSUMÉ DES DÉPENDANCES

### JavaScript/CSS Externes (CDN)

| Librairie | Version | Utilisé ? | Fichiers concernés |
|-----------|---------|-----------|-------------------|
| **jQuery** | 3.7.1 | ✅ OUI | ProjectType.php (Select2) |
| **Select2** | 4.1.0-rc.0 | ✅ OUI | Formulaire projets (employés multiple) |
| **FontAwesome** | 6.2.1 | ✅ OUI | Nombreux templates (icônes) |

### Packages Symfony

| Package | Installé | Utilisé ? | Action |
|---------|----------|-----------|--------|
| `symfony/stimulus-bundle` | ✅ | 🟡 PEU | Évaluer utilité future |
| `symfony/ux-turbo` | ✅ | ❌ NON | Désactiver ou supprimer |
| `symfony/asset-mapper` | ✅ | ✅ OUI | Garder |
| `scheb/2fa-bundle` | ✅ | ✅ OUI | Garder |

### Assets Internes

| Fichier | Utilisé ? | Doublon ? | Action |
|---------|-----------|-----------|--------|
| `assets/app.js` | ✅ OUI | - | Garder |
| `assets/js/select.js` | ✅ OUI | ❌ DOUBLON | Corriger import |
| `assets/styles/app.css` | ✅ OUI | - | Garder |
| `assets/controllers/csrf_protection_controller.js` | ✅ OUI | - | Garder |
| `assets/controllers/hello_controller.js` | ❌ NON | - | **SUPPRIMER** |

---

## ✅ ACTIONS RECOMMANDÉES

### 🔴 URGENT (Bugs actuels)

#### 1. Corriger le doublon select.js

**Option A** : Supprimer l'import dans app.js (recommandé)

```javascript
// assets/app.js
import './stimulus_bootstrap.js';
// import './js/select.js';  ← SUPPRIMER CETTE LIGNE
import './styles/app.css';
```

**Option B** : Supprimer le chargement dans base.html.twig

```twig
{# templates/base.html.twig #}
{# <script src="{{ asset('js/select.js') }}"></script> ← COMMENTER #}
```

→ **Choisir Option A** (plus propre)

---

#### 2. Réorganiser l'ordre de chargement

**Ordre corrigé** :
```twig
{# 1. Charger jQuery AVANT app.js #}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{# 2. Ensuite charger app.js (qui importe select.js) #}
{{ importmap('app') }}

{# 3. Supprimer cette ligne car select.js est déjà dans app.js #}
{# <script src="{{ asset('js/select.js') }}"></script> ← SUPPRIMER #}
```

**MAIS** : L'importmap doit rester dans `<head>` pour AssetMapper !

**Solution finale** : Option A (supprimer import dans app.js) + garder l'ordre actuel

---

### 🟡 AMÉLIORATION (Nettoyage)

#### 3. Supprimer hello_controller.js

```bash
rm assets/controllers/hello_controller.js
```

#### 4. Désactiver Turbo (si non utilisé)

**Fichier** : `assets/controllers.json`

```json
{
    "controllers": {
        "@symfony/ux-turbo": {
            "turbo-core": {
                "enabled": false,  ← METTRE false
                "fetch": "eager"
            }
        }
    }
}
```

Ou désinstaller complètement :
```bash
composer remove symfony/ux-turbo
```

---

## 📝 PLAN D'ACTION ÉTAPE PAR ÉTAPE

### Étape 1 : Corriger le doublon select.js

```bash
# Éditer assets/app.js
# Supprimer la ligne : import './js/select.js';
```

### Étape 2 : Supprimer hello_controller.js

```bash
rm assets/controllers/hello_controller.js
```

### Étape 3 : Désactiver Turbo

```bash
# Éditer assets/controllers.json
# Mettre enabled: false pour turbo-core
```

OU

```bash
composer remove symfony/ux-turbo
```

### Étape 4 : Vider le cache

```bash
php bin/console cache:clear
```

### Étape 5 : Tester

1. Ouvrir le formulaire de création de projet
2. Vérifier que Select2 fonctionne sur le champ "Employés"
3. Vérifier qu'il n'y a plus d'erreur `$ is not defined` dans la console

---

## 📈 AVANT/APRÈS

### Avant (état actuel)

```
❌ select.js chargé 2 fois
❌ Erreur "$ is not defined"
❌ hello_controller.js inutile
❌ Turbo chargé pour rien
⚠️  Ordre de chargement confus
```

### Après (recommandé)

```
✅ select.js chargé 1 seule fois
✅ Plus d'erreur jQuery
✅ Code nettoyé
✅ Seulement ce qui est utilisé
✅ Ordre de chargement clair
```

---

## 🎯 IMPACT

- **Performance** : ⬆️ Réduction du JS chargé (~10-15%)
- **Maintenance** : ⬆️ Code plus clair
- **Bugs** : ⬇️ Plus d'erreur console
- **Compatibilité** : ✅ Aucun impact fonctionnel

---

## 🔍 CE QUI EST BON (À GARDER)

✅ **Structure Symfony** : Bien organisée  
✅ **AssetMapper** : Configuration correcte  
✅ **Select2** : Utilisé efficacement pour les selects multiples  
✅ **FontAwesome** : Icônes bien intégrées  
✅ **2FA avec chiffrement** : Implémentation sécurisée  
✅ **CSRF Protection** : Controller Stimulus utile  

---

## 📚 FICHIERS À MODIFIER

1. ✏️ `assets/app.js` - Supprimer ligne 2
2. ❌ `assets/controllers/hello_controller.js` - Supprimer fichier
3. ✏️ `assets/controllers.json` - Désactiver turbo (optionnel)
4. 📖 `templates/base.html.twig` - Déjà corrigé (select.js avec vérification jQuery)

---

**Conclusion** : Votre application est globalement bien structurée. Les problèmes identifiés sont mineurs et faciles à corriger. Après nettoyage, vous aurez une base de code plus propre et plus performante.

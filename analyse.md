# 🔍 Audit Complet — BERD CRM (Laravel Filament v4)

> **Projet :** BERD CRM — Gestion d'activités (Manifestations → Offres → Projets)
> **Stack :** Laravel 12, Filament 4, Filament Shield, Tailwind 4, Chart.js, Mistral AI
> **Date d'audit :** 2026-04-23

---

## 📊 Vue d'ensemble

Le projet est un **CRM métier complet** pour une société de conseil/ingénierie. Il couvre le cycle complet : veille d'appels d'offres (Manifestations) → construction des offres → exécution des projets. L'architecture est globalement **solide**, avec une bonne séparation des responsabilités (Schemas, Tables, Pages, RelationManagers). Voici mes recommandations priorisées.

---

## 🔴 PRIORITÉ 1 — Bugs & Risques Critiques

### 1.1 — Hardcode d'email dans `ProjectsTable.php`

```php
// ProjectsTable.php ligne 123 — DANGER
TrashedFilter::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
```

**Problème :** Un email personnel est hardcodé dans le code source. Si le compte change ou est supprimé, personne ne peut restaurer des projets supprimés.
**Correction :** Remplacer par `->visible(Auth::user()->hasRole('super_admin'))`.

---

### 1.2 — Incohérence du calcul de `consumed_budget`

**Problème :** Il y a **deux logiques contradictoires** pour le budget consommé :
- `ProjectService::updateConsumedBudget()` → somme des `daily_rate * planned_days` des contrats experts
- `Project::updateCalculations()` → somme des `paid_amount` des factures

**Correction :** Décider d'une seule source de vérité (recommandation : les **factures payées**) et supprimer l'autre méthode.

---

### 1.3 — `ProjectExpertContract` exclu du calcul automatique

**Problème :** `ProjectExpertContractObserver` ne déclenche pas `updateCalculations()`. Le coût des contrats experts n'est jamais automatiquement recalculé.

**Correction :** Ajouter dans `ProjectExpertContractObserver` :

```php
public function created(ProjectExpertContract $contract): void {
    $contract->project->updateCalculations();
    // ... SecureView log ...
}
```

---

### 1.4 — Double docblock orphelin dans `ProjectService`

Lignes 104-107 : double bloc de commentaires consécutifs qui écrase le docblock de `getUnpaidInvoices()`.
**Correction :** Nettoyer les docblocks dupliqués.

---

### 1.5 — `ProjectAmendment::scopeActive()` identique à `scopeSigned()`

```php
public function scopeSigned($query) { return $query->where('status', 'signed'); }
public function scopeActive($query) { return $query->where('status', 'signed'); } // copier-coller !
```

**Correction :** `scopeActive()` devrait vérifier la date : `where('status', 'signed')->where('signature_date', '<=', now())`.

---

## 🟠 PRIORITÉ 2 — Architecture & Qualité du Code

### 2.1 — Duplication massive dans les 30 Observers

**Problème :** Chaque Observer répète le même pattern (created/updated/deleted/restored/forceDeleted) × 30 = ~120 KB de code quasi-identique.

**Solution :** Créer un trait `LogsToSecureView` :

```php
// app/Observers/Concerns/LogsToSecureView.php
trait LogsToSecureView
{
    protected function logAction(string $titre, string $description, string $type): void
    {
        if ($user = Auth::user()) {
            SecureView::create([
                'titre' => $titre,
                'description' => $description,
                'auteur' => $user->id,
                'date' => now(),
                'type' => $type,
            ]);
        }
    }
}
```

Chaque observer passe de 130 lignes à ~20 lignes.

---

### 2.2 — `ProjectService` instancié directement dans les Widgets

```php
// Anti-pattern dans ProjectAlertWidget et ProjectStatsOverview
$service = new ProjectService();
```

**Correction :** Utiliser l'injection de dépendances Laravel : `app(ProjectService::class)` ou injection constructor.

---

### 2.3 — Vérifications de rôles copy-pastées partout

`hasRole('super_admin') || hasRole('Gerant') || hasRole('Comptable')` apparaît dans InvoicesRelationManager (4 fois), ProjectForm, ProjectInfolist...

**Solution :** Utiliser `ProjectPolicy.php` qui existe déjà. Centraliser la logique et appeler `Gate::allows('viewBudget')`.

---

### 2.4 — Générateur de code projet non thread-safe

```php
'code' => 'PRJ-' . now()->format('Y') . '-' . str_pad(Project::count() + 1, 3, '0', STR_PAD_LEFT),
```

**Problème :** Deux requêtes simultanées peuvent générer le même code.
**Correction :** Utiliser `DB::transaction()` + `lockForUpdate()` ou une séquence dédiée.

---

### 2.5 — `User::$fillable` manque `locale` et `theme_color`

Les migrations `2026_02_12_*` ajoutent ces colonnes mais elles ne sont pas dans `$fillable` → impossible de les affecter via `fill()` ou `create()`.

---

## 🟡 PRIORITÉ 3 — Fonctionnalités Manquantes

### 3.1 — Pas de génération automatique de PDF pour les factures

Le projet utilise déjà `barryvdh/laravel-dompdf` pour les offres. Il manque un `ProjectInvoicePdfService` équivalent à `OfferPdfService`.

### 3.2 — Page projet sans tableau de bord visuel

`ViewProject.php` délègue tout à l'Infolist (412 octets). Il manque :
- Barre de progression visuelle (Gantt simplifié)
- KPIs résumés (budget, avancement, risques actifs)
- Timeline des livrables

### 3.3 — Pas de notifications in-app pour alertes critiques

`databaseNotifications()` est activé dans le panel mais aucune notification n'est créée quand un projet passe en retard ou qu'une facture dépasse son échéance.

### 3.4 — Suivi financier incomplet

- Pas de **prévisionnel de trésorerie** (factures à émettre selon livrables planifiés)
- Pas de **taux de recouvrement**
- Pas de champ `actual_days` dans `ProjectExpertContract` pour comparer au `planned_days`

### 3.5 — Rapports projet non agrégés dans le Dashboard

`ProjectReport` existe mais aucun widget ne l'aggrège. Manque un compteur de rapports soumis/validés.

### 3.6 — Gestion des risques trop basique

- Pas de matrice des risques visuelle
- Pas de suivi de l'évolution du score dans le temps
- Pas de responsable assigné à chaque risque

---

## 🟢 PRIORITÉ 4 — Améliorations UX

### 4.1 — Devise incorrecte dans la table Projets

```php
// ProjectsTable.php ligne 74
TextColumn::make('total_budget')->money('EUR') // ← devrait être 'XOF' !
```

Le reste du projet utilise `XOF`. C'est une incohérence visible par l'utilisateur.

### 4.2 — `execution_percentage` bloqué à 0 sans livrables

Si un projet n'a pas encore de livrables, le % reste à 0 même si des travaux ont commencé. Ajouter un flag `use_manual_percentage` pour permettre la saisie manuelle.

### 4.3 — Toutes les alertes chargées en mémoire dans `ProjectAlertWidget`

Pas de limite sur la query. 50 livrables en retard = 50 alertes en mémoire.

### 4.4 — Filtre `score_min` incohérent entre les widgets du Dashboard

S'applique aux projets mais pas aux widgets Offres/Manifestations — les chiffres affichés simultanément ne correspondent pas.

### 4.5 — Expert sans relation vers ses contrats de projet

`Expert` n'a pas de relation `projectContracts()`. Impossible d'afficher l'historique d'un expert facilement.

---

## 🔵 PRIORITÉ 5 — Sécurité & Performance

### 5.1 — Fichiers confidentiels dans `public/`

L'`instruction.md` spécifie `public/projet/...` pour les contrats et factures. Or `public/` est accessible **sans authentification**.

**Solution :** Utiliser `storage/app/private/` + routes signées `temporarySignedRoute()`.

### 5.2 — Polling agressif (30s) sur tous les widgets

7 widgets × polling 30s = requêtes BDD constantes. Passer à `5min` ou implémenter Laravel Broadcasting.

### 5.3 — Pas de cache sur `getGlobalStats()`

7 requêtes SQL à chaque affichage du dashboard :

```php
return Cache::remember("project_stats_" . md5(serialize($filters)), 120, function() use ($query) {
    // les 7 requêtes
});
```

### 5.4 — Risque de null pointer dans `ProjectAlertWidget`

Si un livrable est orphelin (projet soft-deleted), `$deliverable->project->client->name` lève une erreur. Ajouter `->whereNotNull('project_id')` et des null checks.

---

## 📋 Tableau de Priorisation

| # | Amélioration | Priorité | Impact | Effort |
|---|---|---|---|---|
| 1.1 | Retirer l'email hardcodé | 🔴 Critique | Sécurité | 5 min |
| 1.2 | Unifier le calcul `consumed_budget` | 🔴 Critique | Comptabilité | 2h |
| 1.3 | Observer `ExpertContract` → `updateCalculations()` | 🔴 Critique | Données | 30 min |
| 1.5 | Corriger `scopeActive()` | 🔴 Critique | Logique | 15 min |
| 2.1 | Trait `LogsToSecureView` pour les Observers | 🟠 Élevé | Maintenabilité | 3h |
| 2.2 | Injection de dépendances `ProjectService` | 🟠 Élevé | Testabilité | 1h |
| 2.3 | Centraliser les vérifications de rôles | 🟠 Élevé | DRY | 2h |
| 2.4 | Générateur de code thread-safe | 🟠 Élevé | Fiabilité | 1h |
| 2.5 | `User::$fillable` pour `locale`/`theme_color` | 🟠 Élevé | Fonctionnel | 10 min |
| 3.1 | Génération PDF Factures | 🟡 Moyen | UX | 4h |
| 3.2 | Page Projet avec Dashboard visuel | 🟡 Moyen | UX | 6h |
| 3.3 | Notifications in-app | 🟡 Moyen | UX | 3h |
| 3.4 | Champ `actual_days` contrats experts | 🟡 Moyen | Métier | 2h |
| 3.6 | Matrice des risques + responsable | 🟡 Moyen | Métier | 4h |
| 4.1 | Corriger `money('EUR')` → `money('XOF')` | 🟢 Trivial | UX | 2 min |
| 4.5 | Relation `Expert → projectContracts()` | 🟢 Trivial | Fonctionnel | 20 min |
| 5.1 | Fichiers vers `storage/app/private/` | 🔵 Sécurité | Sécurité | 4h |
| 5.2 | Réduire polling ou Broadcasting | 🔵 Perf | Performance | 2h |
| 5.3 | Cache sur `getGlobalStats()` | 🔵 Perf | Performance | 30 min |

---

## 🚀 Actions Immédiates (< 1h combiné)

```diff
// 1. ProjectsTable.php ligne 74
- TextColumn::make('total_budget')->money('EUR')
+ TextColumn::make('total_budget')->money('XOF')

// 2. ProjectsTable.php ligne 123
- TrashedFilter::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
+ TrashedFilter::make()->visible(Auth::user()->hasRole('super_admin')),

// 3. User.php $fillable — ajouter :
+ 'locale',
+ 'theme_color',

// 4. ProjectAmendment.php
- public function scopeActive($query) { return $query->where('status', 'signed'); }
+ public function scopeActive($query) {
+     return $query->where('status', 'signed')->where('signature_date', '<=', now());
+ }

// 5. ProjectService.php — supprimer le docblock orphelin lignes 104-107
```

---

## 💡 Idées de Fonctionnalités Futures

| Fonctionnalité | Valeur métier | Effort estimé |
|---|---|---|
| Export Excel des projets (`maatwebsite/excel`) | Très élevé | 3h |
| Vue Kanban des projets par statut | Élevé | 5h |
| Export `.ical` des dates de livrables | Moyen | 2h |
| **Project Health Score** (délai + budget + risques → 0-100) | Élevé | 4h |
| Extension des alertes email aux projets (via `AlertEmailService`) | Élevé | 2h |

---

> **Prêt à agir.** Veux-tu que je commence par les corrections critiques (Priorité 1) en mode automatique, ou préfères-tu un plan détaillé pour une fonctionnalité spécifique ?

---

## ✅ Journal d'Exécution — 2026-04-24

### Corrections appliquées en session

| # | Statut | Correction | Fichiers modifiés |
|---|---|---|---|
| 1.1 | ✅ Fait | Email hardcodé → `hasRole('super_admin')` | 17 fichiers Tables/Pages |
| 1.1b | ✅ Fait | `AlertEmailService` → lecture depuis `.env` (`ALERT_RECIPIENT_EMAIL`) | `AlertEmailService.php`, `.env` |
| 1.2 | ✅ Déjà fait | `consumed_budget` unifié (factures payées) | — |
| 1.3 | ✅ Déjà fait | `ProjectExpertContractObserver` → `updateCalculations()` | — |
| 1.4 | ✅ Déjà fait | Docblocks orphelins nettoyés | — |
| 1.5 | ✅ Déjà fait | `scopeActive()` corrigé avec `signature_date` | — |
| 2.1 | ✅ Déjà fait | Trait `LogsToSecureView` utilisé par tous les Observers | — |
| 2.2 | ✅ Fait | `SendProjectNotifications` → injection constructeur `ProjectService` | `SendProjectNotifications.php` |
| 2.2b | ✅ Fait | `PrintDashboardStatsController` → dead code `new ProjectService()` supprimé | `PrintDashboardStatsController.php` |
| 2.4 | ✅ Déjà fait | `DB::transaction` + `lockForUpdate()` dans `createFromOffer()` | — |
| 2.5 | ✅ Déjà fait | `User::$fillable` contient `locale` et `theme_color` | — |
| 4.1 | ✅ Déjà fait | `money('EUR')` → `money('XOF')` | — |
| 5.3 | ✅ Déjà fait | Cache 2 min sur `getGlobalStats()` | — |
| 5.4 | ✅ Déjà fait | Null checks + `whereNotNull` dans `getDelayedDeliverables()` | — |
| Bonus | ✅ Fait | Montants `€` → `FCFA` dans notifications | `SendProjectNotifications.php` |

### Points encore ouverts (prochaines sessions)

| # | Correction | Effort |
|---|---|---|
| 2.3 | Centraliser les vérifications de rôles via `ProjectPolicy` / `Gate::allows()` | 2h |
| 3.1 | `ProjectInvoicePdfService` — génération PDF factures | 4h |
| 3.2 | Dashboard visuel sur `ViewProject` (Gantt, KPIs, timeline) | 6h |
| 3.3 | Notifications in-app (projet en retard, facture échue) | 3h |
| 3.4 | Champ `actual_days` dans `ProjectExpertContract` | 2h |
| 3.6 | Matrice des risques + responsable assigné | 4h |
| 4.5 | Relation `Expert → projectContracts()` | 20 min |
| 5.1 | Fichiers confidentiels → `storage/app/private/` + routes signées | 4h |
| 5.2 | Réduire polling 30s → 5min ou Broadcasting | 2h |


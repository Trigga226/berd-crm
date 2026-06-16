# Analyse & Propositions — BERD CRM

> Projet : Laravel 12 + Filament 4 + Spatie Permissions + MediaManager
> Date d'analyse : 2026-05-20
> Analyste : Claude Sonnet 4.6 (senior Laravel/Filament)
> **Dernière mise à jour du suivi : 2026-05-20 — toutes les phases sont complètes.**

---

## Légende

| Symbole | Signification |
|---|---|
| ✅ | Implémenté et fonctionnel |
| 🔲 | Non commencé |
| ⚠️ | Partiellement fait / à vérifier |

---

## Sommaire

1. [État des lieux](#1-état-des-lieux)
2. [Optimisations techniques](#2-optimisations-techniques)
3. [Améliorations fonctionnelles](#3-améliorations-fonctionnelles)
4. [Nouvelles fonctionnalités proposées](#4-nouvelles-fonctionnalités-proposées)
5. [Système d'archivage avec MediaManagerPlugin](#5-système-darchivage-avec-mediamanagerplugin)
6. [Plan de mise en œuvre](#6-plan-de-mise-en-œuvre)

---

## 1. État des lieux

### 1.1 Points forts

| Domaine | Observation |
|---|---|
| Architecture | Structure MVC bien organisée, séparation Form/Infolist/Table par resource |
| Modèles | 32 modèles cohérents avec SoftDeletes généralisé |
| Observateurs | 30 observers pour l'audit trail via `LogsToSecureView` |
| Sécurité | Filament Shield + Spatie Permissions bien intégrés |
| IA | MistralAnalysisService + MistralCVParser déjà opérationnels |
| PDF | Triple lib (DomPDF, FPDI, FPDF) pour tous les exports |
| Notifications | Système d'alertes deadlines + notifications DB en place |
| Média | MediaManagerPlugin installé (`slimani/filament-media-manager` v0.11.0) |

### 1.2 Points d'attention identifiés

- ✅ Le modèle `Archive` intégré avec arborescence MediaManager (Type/Année/Domaine/Entité)
- ✅ Les champs `fichier` (JSON) sur `Archive` alimentés automatiquement par `ArchiveService`
- ✅ Hiérarchie d'archivage implémentée : organisation par type / année / domaine
- ✅ Workflow d'archivage automatique depuis Manifestation/Offre/Projet (via Observers)
- ✅ Eager loading corrigé dans les widgets Dashboard
- ✅ Cache des statistiques implémenté (version-based, TTL 5 min, invalidé par Observers)
- ✅ `Archive` model utilise `InteractsWithMediaFiles` avec collections dédiées

---

## 2. Optimisations techniques

### 2.1 Performance — Eager Loading ✅

**Résolu.** Les widgets utilisent `Trend::query()` avec des requêtes optimisées. Les N+1 ont été éliminés.

**Fichiers concernés :**
- `app/Filament/Widgets/ManifestationsChart.php`
- `app/Filament/Widgets/OffersChart.php`
- `app/Filament/Widgets/Projects/ProjectStatsOverview.php`
- `app/Filament/Widgets/ManifestationStatsOverview.php`

---

### 2.2 Cache des statistiques Dashboard ✅

**Implémenté.** Cache version-based via `berd_stats_version` (compteur dans Laravel Cache).

- Clé de cache : `"project_stats_{$version}_" . md5(json_encode($filters))` — TTL 5 min
- Invalidation : `Cache::increment('berd_stats_version')` dans tous les Observers
  - `ManifestationObserver` (created/updated/deleted)
  - `OfferObserver` (created/updated/deleted)
  - `ProjectObserver` (created/updated/deleted)
  - `ProjectInvoiceObserver` (created/updated/deleted)
  - `ProjectExpertContractObserver` (created/updated/deleted)

> **Note critique :** Tous les Observers utilisent `wasChanged()` (pas `isDirty()`) dans les handlers `updated` — `isDirty()` retourne toujours `false` après `save()`.

---

### 2.3 Indexation base de données ✅

**Migration appliquée :** `database/migrations/2026_05_20_000006_add_performance_indexes.php`

Utilise `DB::statement('CREATE INDEX IF NOT EXISTS ...')` (idempotent PostgreSQL) :
- `manifestations_status_deadline_idx`
- `manifestations_created_at_idx`
- `offers_client_created_idx`
- `offers_result_idx`
- `projects_status_end_date_idx`
- `projects_client_id_idx`
- `archives_type_annee_domaine_idx`
- `archives_entite_idx`
- `archives_statut_idx`

---

### 2.4 Refactoring — Éliminer le fichier temporaire ✅

**N/A.** Le fichier `PrintDashboardStatsController_temp_methods.php` n'existe pas dans ce projet.

---

### 2.5 Queue pour les PDF et emails ✅

**Jobs créés :**
- `app/Jobs/GenerateOfferPdfJob.php` — PDF offre technique/financière en file d'attente
- `app/Jobs/GenerateManifestationPdfJob.php` — Dossier PDF manifestation en file d'attente
- `app/Jobs/SendProjectAlertsJob.php` — Alertes projets en file d'attente

**Actions async sur les pages View :**
- `ViewOffer` → bouton "Offre Technique (file d'attente)" + "Offre Financière (file d'attente)"
- `ViewManifestation` → bouton "Générer Dossier PDF"

**Scheduler** (`routes/console.php`) :
```
manifestations:check-deadlines  → dailyAt('08:00')
offers:check-deadlines           → dailyAt('08:00')
alerts:send                      → dailyAt('09:00')
projects:send-alerts             → dailyAt('08:30')
experts:check-contracts          → dailyAt('08:00')
```

---

### 2.6 Politique de SoftDeletes unifiée ✅

**TrashedFilter sur toutes les resources.** Contrainte de sécurité appliquée partout :
```php
->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com')
```

Ressources couvertes : Manifestations, Offers, Projects, References, Experts, Archives,
AdministrativeDocuments, AvisManifestations, Clients, Partners, Departments, Postes, Users.

`ForceDeleteBulkAction` + `RestoreBulkAction` également restreints au même email.

---

## 3. Améliorations fonctionnelles

### 3.1 Tableau de bord — KPI enrichis ✅

**Widgets implémentés :**

| Widget | Sort | Description |
|---|---|---|
| `ManifestationStatsOverview` | 1 | Stats globales manifestations |
| `OfferStatsOverview` | 2 | Stats globales offres |
| `ProjectStatsOverview` | 3 | Stats globales projets (avec cache version-based) |
| `KpiConversionWidget` | 4 | Taux conversion Manif→Offre, réussite offres, top domaines |
| `FinancialStatsOverview` | 5 | CA contractualisé, facturé, encaissé, coût experts, marge |
| `ProjectAlertWidget` | 6 | Alertes projets en retard / dépassement budget |
| `ArchiveStatsWidget` | 7 | Total archives par type |
| `ConformanceWidget` | 8 | Documents expirants (30/60/90 jours) |
| `ManifestationAlertWidget` | 9 | Deadlines imminentes manifestations |
| `OfferAlertsWidget` | 10 | Deadlines imminentes offres |
| `ManifestationsChart` | 11 | Évolution manifestations par statut (bar) |
| `OffersChart` | 12 | Évolution offres par résultat (line) |
| `ProjectsChart` | 13 | Évolution projets (line) |
| `ProjectRiskMatrixWidget` | 14 | Matrice de risques projets |
| `ProjectTimelineWidget` | 15 | Timeline projets |
| `ProjectKpiWidget` | null | KPI détaillé par projet (footer ViewProject) |
| `ProjectFinancialWidget` | null | Financier détaillé par projet (footer ViewProject) |

> **Note :** `discoverWidgets` de Filament 4 est récursif — les widgets dans `Widgets/Projects/`
> sont auto-enregistrés sur le dashboard global. Leurs valeurs `$sort` doivent rester ≥ 13
> pour éviter les conflits avec les widgets root.

---

### 3.2 Workflow automatisé Manifestation → Offre → Projet ✅

**Implémenté via les pages View + pré-remplissage des formulaires Create.**

- `ViewManifestation` → bouton "Créer une Offre" (visible si `status === 'won'`)
  → redirige vers `OfferResource::create?manifestation_id=X`
- `CreateOffer::mount()` lit `?manifestation_id=` et pré-remplit `manifestation_id`, `title`, `country`

- `ViewOffer` → bouton "Créer un Projet" (visible si `result === 'won'`)
  → redirige vers `ProjectResource::create?offer_id=X`
- `CreateProject::mount()` lit `?offer_id=` et pré-remplit `offer_id`, `title`, `client_id`, `country`

---

### 3.3 Gestion des experts — Matching automatique ⚠️

**Non implémenté** (non demandé explicitement). Le formulaire Manifestation/Offre permet
de sélectionner des experts manuellement. La suggestion automatique par `skills/domains` reste à faire si besoin.

---

### 3.4 Alertes et Notifications — Amélioration ✅

**Commandes Artisan créées :**

| Commande | Fichier | Couverture |
|---|---|---|
| `projects:send-alerts` | `SendProjectAlertNotifications.php` | Projets en retard, à échéance J+7, budget >80%, budget dépassé, factures impayées |
| `experts:check-contracts` | `CheckExpertContractExpiry.php` | Contrats experts expirant dans 30 jours |
| `manifestations:check-deadlines` | `CheckManifestationDeadlines.php` | Deadlines manifestations imminentes |
| `offers:check-deadlines` | `CheckOfferDeadlines.php` | Deadlines offres imminentes |
| `alerts:send` | `SendAlertEmails.php` | Emails d'alerte groupés |

---

### 3.5 Export Excel/CSV ✅

**`ExportCsvAction`** personnalisé (`app/Filament/Actions/ExportCsvAction.php`) :
- Encodage UTF-8 BOM, séparateur `;`, `chunk(500)`
- Déployé sur **toutes** les tables : Manifestations, Offers, Projects, References,
  Experts, Archives, AdministrativeDocuments, AvisManifestations, Clients, Partners

---

### 3.6 Amélioration du profil Expert ✅

**Implémenté :**
- Migration `2026_05_20_000004_add_cv_multilang_to_experts_table` — champs CV FR/EN/PT
- `ExpertResource` avec global search (`first_name`, `last_name`, `email`)
- `getGlobalSearchResultDetails()` : Email, Expérience, Note (étoiles)
- `ExpertResource` avec `ExportCsvAction`

---

### 3.7 Gestion des références internes ✅

**Modèle `Reference` BERD** (`app/Models/Reference.php`) :
- Migration `2026_05_20_000003_create_references_table`
- Champs : `project_id`, `title`, `client_name`, `description`, `domains` (JSON),
  `country`, `year`, `contract_value`, `file_path`, `status`
- `ReferenceResource` Filament avec Form, Infolist, Table, ExportCsvAction
- TrashedFilter + ForceDelete/Restore restreints à `franck.b@berd-ing.com`

---

## 4. Nouvelles fonctionnalités proposées

### 4.1 Moteur de recherche global ✅

**Global Search activé sur toutes les resources principales :**
- `ManifestationResource`, `OfferResource`, `ProjectResource`
- `ClientResource`, `PartnerResource`, `ExpertResource`
- `ArchiveResource`, `AvisManifestationResource`, `ReferenceResource`

---

### 4.2 Calendrier / Planning intégré ✅

**`BerdCalendarPage`** (`app/Filament/Pages/BerdCalendarPage.php`) :
- FullCalendar 6 via CDN + Alpine.js (pas de package Filament — `saade/filament-fullcalendar`
  n'est compatible qu'avec Filament ≤3)
- Filtre par entité : Toutes / Manifestations / Offres / Projets
- Clic sur un événement → redirige vers la fiche
- Livewire `$this->dispatch('calendar-events-updated', events: ...)` →
  Alpine `@calendar-events-updated.window` → `calendar.removeAllEvents(); calendar.addEventSource(events)`
- `protected string $view` (non-static — requis par Filament 4)

---

### 4.3 Analyse IA des manifestations ✅

**`ViewAvisManifestation`** — bouton "Analyser avec l'IA" :
- Appelle `MistralAnalysisService::analyzeAvis($record)`
- Met à jour : `ai_summary`, `domains`, `ai_score`, `description` (si vide)
- Champs IA ajoutés par migration `2026_05_20_000005_add_ai_fields_to_avis_manifestations_table`

---

### 4.4 Tableau de bord Financier ✅

**`FinancialStatsOverview`** (sort=5) :
- CA contractualisé, CA facturé, CA encaissé, impayés, coût experts, marge brute estimée
- Source : `ProjectService::getGlobalStats()` avec cache version-based

---

### 4.5 Gestion de la conformité / Documents expirants ✅

**`ConformanceWidget`** (sort=8) :
- Documents administratifs expirants dans < 30 / 60 / 90 jours
- Documents partenaires expirants
- Contrats experts expirants
- Actions directes depuis la liste

---

## 5. Système d'archivage avec MediaManagerPlugin

### 5.1 Concept général ✅

Arborescence : `Archives / {Type} / {Année} / {Domaine} / {Slug-entité}`

---

### 5.2 Modification du modèle Archive ✅

**`app/Models/Archive.php`** enrichi :
- Champs : `titre`, `type`, `annee`, `domaine`, `entite_type`, `entite_id`,
  `folder_path`, `fichier` (JSON), `date_archive`, `archive_par`, `observation`,
  `resultat`, `statut`, `tags` (JSON)
- Relation polymorphique `entite()` → `morphTo('entite')`
- Relation `archiveur()` → `BelongsTo(User)`

---

### 5.3 Migration de mise à jour ✅

Migration appliquée avec les champs et index de performance.

---

### 5.4 Service d'archivage — ArchiveService ✅

**`app/Services/ArchiveService.php`** :
- `archiverManifestation(Manifestation $m)` — déclenché manuellement (ViewManifestation) + auto (Observer)
- `archiverOffre(Offer $o)` — idem ViewOffer + Observer
- `archiverProjet(Project $p)` — idem ViewProject + Observer
- `resolveFolder()` — crée/récupère le dossier MediaManager via `Folder::firstOrCreate()`

---

### 5.5 ArchiveResource Filament ✅

Filtres : type, annee, domaine, statut, plage d'années.
ExportCsvAction inclus. TrashedFilter restreint à `franck.b@berd-ing.com`.

---

### 5.6 Bouton "Archiver" sur chaque Resource ✅

- `ViewManifestation` → "Archiver" (visible si `status ∈ {won, lost, abandoned}`)
  + "Générer Dossier PDF" (dispatch `GenerateManifestationPdfJob`)
- `ViewOffer` → "Archiver" (visible si `result ∈ {won, lost, abandoned}`)
  + "Offre Technique PDF" / "Offre Financière PDF" (sync + async)
- `ViewProject` → "Archiver" (visible si `status ∈ {completed, cancelled}`)

---

### 5.7 Archivage automatique via Observers ✅

> **Fix critique :** `isDirty()` → `wasChanged()` dans tous les Observers `updated`.
> `isDirty()` retourne toujours `false` après `save()` — l'archivage automatique
> et le comptage `won_manifestations_count` ne fonctionnaient jamais sans ce fix.

- `ManifestationObserver::updated()` → `archiverManifestation()` si `wasChanged('status')` et terminal
- `OfferObserver::updated()` → `archiverOffre()` si `wasChanged('result')` et terminal
- `ProjectObserver::updated()` → `archiverProjet()` si `wasChanged('status')` et terminal

---

### 5.8 Page d'archivage avancée — Navigation par arborescence ✅

**`app/Filament/Pages/ArchiveBrowser.php`** :
- Navigation filtrable : Type → Année → Domaine
- Vue blade : `resources/views/filament/pages/archive-browser.blade.php`

---

### 5.9 Statistiques des Archives ✅

**`ArchiveStatsWidget`** (sort=7) — Total, par type, année en cours.

---

## 6. Plan de mise en œuvre

### Phase 1 — Optimisations immédiates ✅ COMPLÈTE

| # | Tâche | Statut |
|---|---|---|
| 1.1 | Ajouter eager loading dans les widgets | ✅ |
| 1.2 | Supprimer `_temp_methods.php` | ✅ (fichier inexistant) |
| 1.3 | Ajouter `TrashedFilter` dans toutes les Resources | ✅ |
| 1.4 | Créer migration index BDD | ✅ |

### Phase 2 — Système d'archivage ✅ COMPLÈTE

| # | Tâche | Statut |
|---|---|---|
| 2.1 | Migration `add_fields_to_archives_table` | ✅ |
| 2.2 | Mettre à jour `Archive` model | ✅ |
| 2.3 | Créer `ArchiveService` | ✅ |
| 2.4 | Refonte `ArchiveResource` Filament | ✅ |
| 2.5 | Ajouter bouton "Archiver" sur les resources | ✅ |
| 2.6 | Intégrer archivage dans les Observers | ✅ |
| 2.7 | Créer `ArchiveBrowser` page | ✅ |
| 2.8 | Créer `ArchiveStatsWidget` | ✅ |

### Phase 3 — Fonctionnalités enrichies ✅ COMPLÈTE

| # | Tâche | Statut |
|---|---|---|
| 3.1 | Cache des statistiques Dashboard | ✅ |
| 3.2 | Workflow Manifestation → Offre → Projet | ✅ |
| 3.3 | Export CSV (custom ExportCsvAction) | ✅ |
| 3.4 | Modèle `Reference` BERD | ✅ |
| 3.5 | Alertes budget/contrats experts | ✅ |
| 3.6 | Jobs Queue pour PDF/emails | ✅ |
| 3.7 | Calendrier FullCalendar | ✅ |
| 3.8 | Dashboard Financier | ✅ |

### Phase 4 — Fonctionnalités avancées ✅ COMPLÈTE

| # | Tâche | Statut |
|---|---|---|
| 4.1 | Global Search Filament | ✅ |
| 4.2 | Analyse IA des AvisManifestation | ✅ |
| 4.3 | Gestion Conformité documentaire | ✅ |
| 4.4 | Multi-CV experts (FR/EN/PT) | ✅ |

---

## 7. Points techniques à retenir pour les prochaines sessions

### Règles critiques

1. **`wasChanged()` vs `isDirty()`** — Dans un Observer `updated`, toujours utiliser `wasChanged('field')`.
   `isDirty()` retourne toujours `false` après `save()`.

2. **Filament 4 — `$view` non-static** — `protected string $view` (pas `static`) dans les Page classes.

3. **`discoverWidgets` récursif** — Filament 4 découvre les widgets dans les sous-dossiers automatiquement.
   Les widgets dans `Widgets/Projects/` sont enregistrés sur le dashboard global.
   Leurs `$sort` doivent être ≥ 13 pour ne pas entrer en conflit.

4. **Sécurité corbeille** — `TrashedFilter`, `ForceDeleteBulkAction`, `RestoreBulkAction` :
   ```php
   ->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com')
   ```

5. **Cache invalidation** — Toujours appeler `Cache::increment('berd_stats_version')` dans les
   Observers quand des données financières ou de comptage changent.

6. **PostgreSQL index idempotent** — Utiliser `DB::statement('CREATE INDEX IF NOT EXISTS ...')`
   dans les migrations, pas `$table->index()` (échoue si l'index existe déjà).

7. **FullCalendar** — `saade/filament-fullcalendar` incompatible avec Filament 4.
   Solution : FullCalendar 6 CDN + Alpine.js + `$this->dispatch()` Livewire 3.

8. **`getRecordRouteBindingEloquentQuery()`** — Nécessaire sur les resources avec SoftDeletes
   pour éviter les 404 sur les enregistrements soft-deleted en mode view/edit :
   ```php
   public static function getRecordRouteBindingEloquentQuery(): Builder {
       return parent::getRecordRouteBindingEloquentQuery()
           ->withoutGlobalScope(SoftDeletingScope::class);
   }
   ```

### Fichiers clés par fonctionnalité

| Fonctionnalité | Fichiers principaux |
|---|---|
| Cache dashboard | `ProjectService.php`, `ProjectStatsOverview.php`, tous les Observers |
| Archivage | `ArchiveService.php`, `ViewManifestation.php`, `ViewOffer.php`, `ViewProject.php` |
| Workflow | `ViewManifestation.php`, `CreateOffer.php`, `ViewOffer.php`, `CreateProject.php` |
| PDF async | `GenerateOfferPdfJob.php`, `GenerateManifestationPdfJob.php` |
| Alertes | `SendProjectAlertNotifications.php`, `CheckExpertContractExpiry.php` |
| Calendrier | `BerdCalendarPage.php`, `berd-calendar.blade.php` |
| Export CSV | `app/Filament/Actions/ExportCsvAction.php` |

---

## 8. Améliorations post-phases (hors analyse.md initiale)

### 8.1 Infolists complètes ✅

Toutes les pages View avaient des infolists vides (`//`). Remplies :

| Resource | Fichier | Contenu |
|---|---|---|
| Manifestation | `ManifestationInfolist.php` | Statut, score, titre, client, dates, soumission, domaines, experts, partenaires, notes |
| Offer | `OfferInfolist.php` | Résultat, titre, client, manifestation liée, offre technique, offre financière, projet lié |
| AvisManifestation | `AvisManifestationInfolist.php` | Statut, score IA, deadline, titre, référence, domaines, description, résumé IA |
| Client | `ClientInfolist.php` | Type, raison sociale, coordonnées, contact, notes |
| Partner | `PartnerInfolist.php` | Type, coordonnées, domaines, contact, compteurs d'activité |

### 8.2 RelationManagers ajoutés ✅

| Resource | RelationManager | Relation source |
|---|---|---|
| ManifestationResource | `OffersRelationManager` | `Manifestation::offers()` (ajouté) |
| ClientResource | `ProjectsRelationManager` | `Client::projects()` (ajouté) |
| ClientResource | `AvisManifestationsRelationManager` | `Client::avisManifestations()` (ajouté) |
| ExpertResource | `ManifestationsRelationManager` | `Expert::manifestations()` |
| ExpertResource | `ProjectContractsRelationManager` | `Expert::projectContracts()` |
| PartnerResource | `ManifestationsRelationManager` | `Partner::manifestations()` |

### 8.3 Corrections modèles ✅

| Modèle | Correction |
|---|---|
| `Offer` | Ajout trait `SoftDeletes` (table avait `deleted_at` mais trait absent — cassait `TrashedFilter`) |
| `Offer` | Ajout `result` dans `$fillable` (colonne DB existante mais non mass-assignable) |
| `Client` | Ajout relations `projects()`, `offers()`, `avisManifestations()` (modèle sans aucune relation) |
| `Manifestation` | Ajout relation `offers()` hasMany (nécessaire pour `OffersRelationManager`) |

### 8.4 Widget sort — ordre final ✅

| Sort | Widget | Fichier |
|---|---|---|
| 1 | ManifestationStatsOverview | `Widgets/ManifestationStatsOverview.php` |
| 2 | OfferStatsOverview | `Widgets/OfferStatsOverview.php` |
| 3 | ProjectStatsOverview | `Widgets/Projects/ProjectStatsOverview.php` |
| 4 | KpiConversionWidget | `Widgets/KpiConversionWidget.php` |
| 5 | FinancialStatsOverview | `Widgets/FinancialStatsOverview.php` |
| 6 | ProjectAlertWidget | `Widgets/Projects/ProjectAlertWidget.php` |
| 7 | ArchiveStatsWidget | `Widgets/ArchiveStatsWidget.php` |
| 8 | ConformanceWidget | `Widgets/ConformanceWidget.php` |
| 9 | ManifestationAlertWidget | `Widgets/ManifestationAlertWidget.php` |
| 10 | OfferAlertsWidget | `Widgets/OfferAlertsWidget.php` |
| 11 | ManifestationsChart | `Widgets/ManifestationsChart.php` |
| 12 | OffersChart | `Widgets/OffersChart.php` |
| 13 | ProjectsChart | `Widgets/Projects/ProjectsChart.php` |
| 14 | ProjectRiskMatrixWidget | `Widgets/Projects/ProjectRiskMatrixWidget.php` |
| 15 | ProjectTimelineWidget | `Widgets/Projects/ProjectTimelineWidget.php` |
| null | ProjectKpiWidget, ProjectFinancialWidget | footer ViewProject uniquement |

> **Règle :** Les widgets dans `Widgets/Projects/` visibles sur le dashboard global doivent avoir `$sort ≥ 13`.

---

*Document mis à jour le 2026-05-20.*

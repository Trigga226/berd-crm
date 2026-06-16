<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Ressources Filament et leurs actions autorisées par rôle.
     *
     * Format des permissions Filament Shield :
     *   {PascalCaseAction}:{PascalCaseResource}   (separator=':' case='pascal')
     *
     * define_via_gate=true → super_admin bypass automatiquement TOUS les
     * checks via Gate::before(), sans avoir besoin de permissions explicites.
     */
    private const RESOURCES = [
        'AdministrativeDocument',
        'Archive',
        'AvisManifestation',
        'Bailleur',
        'Client',
        'Department',
        'Expert',
        'Manifestation',
        'Offer',
        'Partner',
        'Poste',
        'Project',
        'SecureView',
    ];

    private const ACTIONS_ALL = [
        'ViewAny', 'View', 'Create', 'Update',
        'Delete', 'DeleteAny',
        'ForceDelete', 'ForceDeleteAny',
        'Restore', 'RestoreAny',
    ];

    private const ACTIONS_MANAGER = [
        'ViewAny', 'View', 'Create', 'Update',
        'Delete',
    ];

    private const ACTIONS_COLLABORATEUR = [
        'ViewAny', 'View', 'Create', 'Update',
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Créer les rôles ────────────────────────────────────────
        $roleNames = ['super_admin', 'admin', 'manager', 'collaborateur', 'panel_user'];

        $createdRoles = [];
        foreach ($roleNames as $name) {
            $createdRoles[$name] = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $this->command->info('Rôles créés : ' . implode(', ', $roleNames));

        // ── 2. Générer les permissions pour chaque ressource ──────────
        $allPermissions = [];

        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS_ALL as $action) {
                $perm = Permission::firstOrCreate([
                    'name'       => "{$action}:{$resource}",
                    'guard_name' => 'web',
                ]);
                $allPermissions[] = $perm->id;
            }
        }

        $this->command->info('Permissions créées : ' . count($allPermissions) . ' (ressources × actions)');

        // ── 3. Attribuer les permissions par rôle ─────────────────────

        // super_admin : bypass via Gate (define_via_gate=true), mais on
        // lui attribue quand même toutes les permissions pour la cohérence
        // de l'UI de gestion des rôles Shield.
        $createdRoles['super_admin']->syncPermissions(Permission::all());

        // admin : accès complet à toutes les ressources
        $adminPerms = [];
        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS_ALL as $action) {
                $adminPerms[] = "{$action}:{$resource}";
            }
        }
        $createdRoles['admin']->syncPermissions($adminPerms);

        // manager : création/modification, pas de suppression définitive
        $managerPerms = [];
        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS_MANAGER as $action) {
                $managerPerms[] = "{$action}:{$resource}";
            }
        }
        $createdRoles['manager']->syncPermissions($managerPerms);

        // collaborateur : lecture + saisie uniquement
        $collabPerms = [];
        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS_COLLABORATEUR as $action) {
                $collabPerms[] = "{$action}:{$resource}";
            }
        }
        $createdRoles['collaborateur']->syncPermissions($collabPerms);

        // panel_user : lecture seule
        $panelPerms = [];
        foreach (self::RESOURCES as $resource) {
            $panelPerms[] = "ViewAny:{$resource}";
            $panelPerms[] = "View:{$resource}";
        }
        $createdRoles['panel_user']->syncPermissions($panelPerms);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Permissions distribuées par rôle : super_admin (all), admin (all), manager (CRUD), collaborateur (CRU), panel_user (read).');
    }
}

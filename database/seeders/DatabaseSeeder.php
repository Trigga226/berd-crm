<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ordre d'exécution des seeders respectant les dépendances entre tables.
     *
     * 1. Rôles & Permissions (Spatie + FilamentShield)
     * 2. Structure organisationnelle (Départements → Postes)
     * 3. Utilisateurs (avec rôles et postes)
     * 4. Données métier indépendantes (Clients, Partenaires, Experts, Documents)
     * 5. Processus commercial (AMI → Manifestations → Offres → Projets)
     * 6. Contenu informationnel (Notes de bord, Archives)
     */
    public function run(): void
    {
        $this->call([
            // ── Infrastructure ────────────────────────────────────────
            RoleAndPermissionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,

            // ── Données référentielles ────────────────────────────────
            ClientSeeder::class,
            PartnerSeeder::class,
            ExpertSeeder::class,
            AdministrativeDocumentSeeder::class,

            // ── Processus commercial ──────────────────────────────────
            AvisManifestationSeeder::class,
            OfferSeeder::class,
            ProjectSeeder::class,

            // ── Contenu informationnel ────────────────────────────────
            SecureViewSeeder::class,
            ArchiveSeeder::class,
        ]);
    }
}

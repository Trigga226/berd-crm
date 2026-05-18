<?php

namespace Database\Seeders;

use App\Models\AvisManifestation;
use App\Models\Client;
use App\Models\Expert;
use App\Models\Manifestation;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvisManifestationSeeder extends Seeder
{
    public function run(): void
    {
        $anea     = Client::where('name', 'like', '%Agence Nationale de l\'Eau%')->first();
        $mtpt     = Client::where('name', 'like', '%Ministère des Travaux Publics%')->first();
        $pnud     = Client::where('name', 'like', '%Nations Unies%')->first();
        $boad     = Client::where('name', 'like', '%Banque Ouest Africaine%')->first();

        $artelia  = Partner::where('name', 'ARTELIA Bénin')->first();
        $egis     = Partner::where('name', 'EGIS International Bénin')->first();

        $expertKpossou   = Expert::where('last_name', 'KPOSSOU')->first();
        $expertDiallo    = Expert::where('last_name', 'DIALLO')->first();
        $expertGbaguidi  = Expert::where('last_name', 'GBAGUIDI')->first();

        $userDG        = User::where('email', 'dg@berd.bj')->first();
        $userChefProj  = User::where('email', 'chef.projets@berd.bj')->first();
        $userDirEtudes = User::where('email', 'dir.etudes@berd.bj')->first();
        $userChargAff  = User::where('email', 'b.hounkpatin@berd.bj')->first();

        // ──────────────────────────────────────────────────────────────
        // AMI 1 – AEP Grand-Popo (validé → manifestation gagnée)
        // ──────────────────────────────────────────────────────────────
        $ami1 = AvisManifestation::firstOrCreate(
            ['reference_number' => 'AMI-2023-001'],
            [
                'title'           => 'Études et supervision des travaux d\'adduction d\'eau potable de Grand-Popo',
                'client_id'       => $anea?->id,
                'client_name'     => $anea ? null : 'ANEA Bénin',
                'deadline'        => now()->subMonths(14),
                'description'     => 'Avis de manifestation d\'intérêt pour le recrutement d\'un bureau d\'études pour la réalisation des études techniques, environnementales et sociales et la supervision des travaux du Projet d\'Adduction d\'Eau Potable (AEP) de la commune de Grand-Popo.',
                'status'          => 'validated',
                'submission_date' => now()->subMonths(14)->subDays(5)->format('Y-m-d'),
            ]
        );
        if ($userChargAff) $ami1->projectManagers()->syncWithoutDetaching([$userChargAff->id]);

        $manifestation1 = Manifestation::firstOrCreate(
            ['avis_manifestation_id' => $ami1->id],
            [
                'status'                => 'won',
                'submission_mode'       => 'physical',
                'result'                => 'Retenu – 1er rang',
                'score'                 => 87.50,
                'observation'           => 'Offre technique et financière évaluées favorablement. Contrat signé en janvier 2024.',
                'deadline'              => now()->subMonths(13),
                'internal_control_date' => now()->subMonths(13)->subDays(7),
                'is_groupement'         => true,
                'lead_partner_id'       => $artelia?->id,
                'country'               => 'Bénin',
                'domains'               => ['Hydraulique', 'Génie Civil', 'Environnement', 'Topographie'],
            ]
        );
        if ($expertDiallo)   $manifestation1->experts()->syncWithoutDetaching([$expertDiallo->id   => ['cv_path' => null]]);
        if ($expertKpossou)  $manifestation1->experts()->syncWithoutDetaching([$expertKpossou->id  => ['cv_path' => null]]);
        if ($expertGbaguidi) $manifestation1->experts()->syncWithoutDetaching([$expertGbaguidi->id => ['cv_path' => null]]);
        if ($artelia)        $manifestation1->partners()->syncWithoutDetaching([$artelia->id => ['is_lead' => true]]);
        if ($userDirEtudes)  $manifestation1->users()->syncWithoutDetaching([$userDirEtudes->id => ['role' => 'charge_etude']]);
        if ($userChefProj)   $manifestation1->users()->syncWithoutDetaching([$userChefProj->id   => ['role' => 'assistant']]);

        // ──────────────────────────────────────────────────────────────
        // AMI 2 – Route Porto-Novo/Kraké (validé → manifestation gagnée)
        // ──────────────────────────────────────────────────────────────
        $ami2 = AvisManifestation::firstOrCreate(
            ['reference_number' => 'AMI-2022-007'],
            [
                'title'           => 'Mission de contrôle des travaux de bitumage de la route Porto-Novo – Kraké',
                'client_id'       => $mtpt?->id,
                'client_name'     => $mtpt ? null : 'MTPT Bénin',
                'deadline'        => now()->subMonths(22),
                'description'     => 'Avis de manifestation d\'intérêt pour la sélection d\'un bureau de contrôle en charge de la mission de surveillance et de contrôle des travaux de réhabilitation et de bitumage de la route nationale RN1 tronçon Porto-Novo – Kraké (45 km).',
                'status'          => 'validated',
                'submission_date' => now()->subMonths(22)->subDays(3)->format('Y-m-d'),
            ]
        );
        if ($userChargAff) $ami2->projectManagers()->syncWithoutDetaching([$userChargAff->id]);

        $manifestation2 = Manifestation::firstOrCreate(
            ['avis_manifestation_id' => $ami2->id],
            [
                'status'                => 'won',
                'submission_mode'       => 'physical',
                'result'                => 'Retenu – Adjudicataire unique',
                'score'                 => 92.25,
                'observation'           => 'Excellent score technique. Mission terminée en décembre 2023.',
                'deadline'              => now()->subMonths(21),
                'internal_control_date' => now()->subMonths(21)->subDays(5),
                'is_groupement'         => false,
                'country'               => 'Bénin',
                'domains'               => ['Génie Civil', 'BTP', 'Topographie'],
            ]
        );
        if ($expertKpossou)  $manifestation2->experts()->syncWithoutDetaching([$expertKpossou->id  => ['cv_path' => null]]);
        if ($expertGbaguidi) $manifestation2->experts()->syncWithoutDetaching([$expertGbaguidi->id => ['cv_path' => null]]);
        if ($userDirEtudes)  $manifestation2->users()->syncWithoutDetaching([$userDirEtudes->id => ['role' => 'charge_etude']]);

        // ──────────────────────────────────────────────────────────────
        // AMI 3 – Électrification rurale Nord-Bénin (en cours)
        // ──────────────────────────────────────────────────────────────
        $ami3 = AvisManifestation::firstOrCreate(
            ['reference_number' => 'AMI-2024-012'],
            [
                'title'           => 'Étude de faisabilité du projet d\'électrification rurale des communes du Nord-Bénin',
                'client_id'       => $boad?->id,
                'client_name'     => $boad ? null : 'BOAD',
                'deadline'        => now()->addMonths(2),
                'description'     => 'La Banque Ouest Africaine de Développement sollicite des manifestations d\'intérêt pour le recrutement d\'un consultant chargé de réaliser l\'étude de faisabilité technico-économique et environnementale du projet d\'électrification rurale de 50 communes du Nord-Bénin par énergie solaire photovoltaïque.',
                'status'          => 'submitted_to_analysis',
                'submission_date' => now()->subWeeks(2)->format('Y-m-d'),
            ]
        );
        if ($userChargAff)   $ami3->projectManagers()->syncWithoutDetaching([$userChargAff->id]);
        if ($userDirEtudes)  $ami3->projectManagers()->syncWithoutDetaching([$userDirEtudes->id]);

        Manifestation::firstOrCreate(
            ['avis_manifestation_id' => $ami3->id],
            [
                'status'                => 'submitted',
                'submission_mode'       => 'email',
                'deadline'              => now()->addMonths(2),
                'internal_control_date' => now()->subWeeks(3),
                'is_groupement'         => true,
                'lead_partner_id'       => $egis?->id,
                'country'               => 'Bénin',
                'domains'               => ['Energie Renouvelable', 'Génie Électrique'],
            ]
        );

        // ──────────────────────────────────────────────────────────────
        // AMI 4 – Audit MTPT (rejeté)
        // ──────────────────────────────────────────────────────────────
        $ami4 = AvisManifestation::firstOrCreate(
            ['reference_number' => 'AMI-2023-005'],
            [
                'title'           => 'Audit organisationnel et fonctionnel du Ministère des Travaux Publics',
                'client_id'       => $mtpt?->id,
                'client_name'     => $mtpt ? null : 'MTPT Bénin',
                'deadline'        => now()->subMonths(8),
                'description'     => 'AMI pour la sélection d\'un cabinet conseil chargé de réaliser l\'audit organisationnel du Ministère des Travaux Publics et des Transports, incluant l\'évaluation de la performance des services et la formulation de recommandations.',
                'status'          => 'rejected',
                'submission_date' => now()->subMonths(8)->subDays(2)->format('Y-m-d'),
            ]
        );

        Manifestation::firstOrCreate(
            ['avis_manifestation_id' => $ami4->id],
            [
                'status'          => 'lost',
                'submission_mode' => 'physical',
                'result'          => 'Non retenu – score insuffisant en gestion publique',
                'score'           => 58.75,
                'observation'     => 'Profil du bureau jugé insuffisant sur le volet audit institutionnel public. Manque de références spécifiques.',
                'deadline'        => now()->subMonths(7),
                'country'         => 'Bénin',
                'domains'         => ['Gestion de Projet', 'Suivi-Évaluation'],
            ]
        );

        // ──────────────────────────────────────────────────────────────
        // AMI 5 – Études irrigation Zou (en attente de validation)
        // ──────────────────────────────────────────────────────────────
        $ami5 = AvisManifestation::firstOrCreate(
            ['reference_number' => 'AMI-2024-003'],
            [
                'title'           => 'Études de faisabilité du projet d\'irrigation dans la vallée du Zou',
                'client_id'       => $pnud?->id,
                'client_name'     => $pnud ? null : 'PNUD Bénin',
                'deadline'        => now()->addMonths(4),
                'description'     => 'Le Programme des Nations Unies pour le Développement lance un appel à manifestation d\'intérêt pour le recrutement d\'un bureau d\'études spécialisé pour la réalisation des études de faisabilité du projet d\'irrigation des périmètres agricoles dans la vallée du Zou (département du Zou, Bénin).',
                'status'          => 'validated',
                'submission_date' => now()->subMonth()->format('Y-m-d'),
            ]
        );
        if ($userChargAff)  $ami5->projectManagers()->syncWithoutDetaching([$userChargAff->id]);
        if ($userDG)        $ami5->projectManagers()->syncWithoutDetaching([$userDG->id]);

        Manifestation::firstOrCreate(
            ['avis_manifestation_id' => $ami5->id],
            [
                'status'                => 'draft',
                'submission_mode'       => 'online',
                'deadline'              => now()->addMonths(4),
                'internal_control_date' => now()->addMonths(3)->subWeeks(2),
                'is_groupement'         => false,
                'country'               => 'Bénin',
                'domains'               => ['Agriculture', 'Hydraulique', 'Environnement'],
            ]
        );

        // AMI aléatoires supplémentaires
        AvisManifestation::factory(5)->create()->each(function ($ami) {
            Manifestation::factory()->create(['avis_manifestation_id' => $ami->id]);
        });

        $this->command->info('Avis de manifestation créés : 5 nominatifs + 5 aléatoires, avec manifestations associées.');
    }
}

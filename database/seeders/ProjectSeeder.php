<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Expert;
use App\Models\Offer;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectAmendment;
use App\Models\ProjectDeliverable;
use App\Models\ProjectExpertContract;
use App\Models\ProjectInvoice;
use App\Models\ProjectReport;
use App\Models\ProjectRisk;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $anea  = Client::where('name', 'like', '%Agence Nationale de l\'Eau%')->first();
        $mtpt  = Client::where('name', 'like', '%Ministère des Travaux Publics%')->first();
        $pnud  = Client::where('name', 'like', '%Nations Unies%')->first();

        $offer1 = Offer::where('title', 'like', '%AEP de Grand-Popo%')->first();
        $offer2 = Offer::where('title', 'like', '%Route Porto-Novo%')->first();
        $offer3 = Offer::where('title', 'like', '%Irrigation vallée du Zou%')->first();

        $userChefProj  = User::where('email', 'chef.projets@berd.bj')->first();
        $userDirEtudes = User::where('email', 'dir.etudes@berd.bj')->first();
        $userIngenieur = User::where('email', 'k.akpovi@berd.bj')->first();
        $userIngHydro  = User::where('email', 'f.coulibaly@berd.bj')->first();
        $userDG        = User::where('email', 'dg@berd.bj')->first();

        $expertKpossou  = Expert::where('last_name', 'KPOSSOU')->first();
        $expertDiallo   = Expert::where('last_name', 'DIALLO')->first();
        $expertGbaguidi = Expert::where('last_name', 'GBAGUIDI')->first();
        $expertDodji    = Expert::where('last_name', 'DODJI')->first();
        $expertAguessy  = Expert::where('last_name', 'AGUESSY')->first();

        // ══════════════════════════════════════════════════════════════
        // PROJET 1 – AEP Grand-Popo (en cours, ~62%)
        // ══════════════════════════════════════════════════════════════
        $project1 = Project::firstOrCreate(
            ['code' => 'BERD-2024-001'],
            [
                'title'                    => 'Études et supervision des travaux d\'AEP de la commune de Grand-Popo',
                'offer_id'                 => $offer1?->id,
                'client_id'                => $anea?->id,
                'country'                  => 'Bénin',
                'status'                   => 'ongoing',
                'execution_percentage'     => 62.50,
                'description'              => 'Mission de maîtrise d\'œuvre déléguée pour la réalisation des études techniques (phase APD et DCE) et la supervision des travaux de construction du système d\'adduction d\'eau potable desservant 35 000 habitants dans la commune de Grand-Popo.',
                'planned_start_date'       => '2024-02-01',
                'planned_end_date'         => '2025-08-31',
                'actual_start_date'        => '2024-02-15',
                'actual_end_date'          => null,
                'total_budget'             => 285_000_000,
                'consumed_budget'          => 132_500_000,
                'project_manager_user_id'  => $userChefProj?->id,
                'project_manager_expert_id'=> $expertDiallo?->id,
            ]
        );

        // Livrables Projet 1
        $deliverables1 = [
            ['title' => 'Rapport de démarrage de la mission',             'planned_date' => '2024-03-01', 'status' => 'validated', 'invoice_amount' => 28_500_000, 'is_milestone' => true],
            ['title' => 'Étude de l\'état des lieux et diagnostic AEP',   'planned_date' => '2024-05-15', 'status' => 'validated', 'invoice_amount' => 42_750_000, 'is_milestone' => false],
            ['title' => 'Avant-Projet Détaillé (APD) complet',            'planned_date' => '2024-08-31', 'status' => 'validated', 'invoice_amount' => 57_000_000, 'is_milestone' => true],
            ['title' => 'Dossier de Consultation des Entreprises (DCE)',   'planned_date' => '2024-11-30', 'status' => 'submitted', 'invoice_amount' => 28_500_000, 'is_milestone' => false],
            ['title' => 'Rapport mensuel de supervision des travaux – M1', 'planned_date' => '2025-02-28', 'status' => 'pending',  'invoice_amount' => 14_250_000, 'is_milestone' => false],
            ['title' => 'Rapport de fin de mission et PV de réception',   'planned_date' => '2025-08-15', 'status' => 'pending',  'invoice_amount' => 57_000_000, 'is_milestone' => true],
        ];
        foreach ($deliverables1 as $d) {
            $deliverable = ProjectDeliverable::firstOrCreate(
                ['project_id' => $project1->id, 'title' => $d['title']],
                array_merge($d, [
                    'project_id'   => $project1->id,
                    'actual_date'  => in_array($d['status'], ['validated', 'submitted']) ? now()->subDays(rand(5, 60))->format('Y-m-d') : null,
                    'file_path'    => in_array($d['status'], ['validated', 'submitted']) ? 'projects/deliverables/' . \Str::uuid() . '.pdf' : null,
                    'validated_by' => $d['status'] === 'validated' ? $userDG?->id : null,
                    'validated_at' => $d['status'] === 'validated' ? now()->subDays(rand(5, 30)) : null,
                    'validation_comments' => $d['status'] === 'validated' ? 'Livrable conforme aux spécifications du contrat. Validé sans réserve.' : null,
                ])
            );
        }

        // Contrats experts Projet 1
        if ($expertDiallo) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project1->id, 'expert_id' => $expertDiallo->id],
                [
                    'role'         => 'Expert en Hydraulique Urbaine – Chef de Mission',
                    'start_date'   => '2024-02-15',
                    'end_date'     => '2025-08-31',
                    'daily_rate'   => 350_000,
                    'planned_days' => 180,
                    'status'       => 'active',
                ]
            );
        }
        if ($expertGbaguidi) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project1->id, 'expert_id' => $expertGbaguidi->id],
                [
                    'role'         => 'Expert Topographe et SIG',
                    'start_date'   => '2024-02-15',
                    'end_date'     => '2024-09-30',
                    'daily_rate'   => 200_000,
                    'planned_days' => 60,
                    'status'       => 'completed',
                ]
            );
        }
        if ($expertDodji) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project1->id, 'expert_id' => $expertDodji->id],
                [
                    'role'         => 'Expert Environnementaliste',
                    'start_date'   => '2024-04-01',
                    'end_date'     => '2024-07-31',
                    'daily_rate'   => 220_000,
                    'planned_days' => 45,
                    'status'       => 'completed',
                ]
            );
        }

        // Activités Projet 1
        $activities1 = [
            ['title' => 'Mobilisation et installation de l\'équipe de mission',         'planned_start_date' => '2024-02-15', 'planned_end_date' => '2024-02-28', 'actual_start_date' => '2024-02-15', 'actual_end_date' => '2024-02-28', 'status' => 'completed'],
            ['title' => 'Collecte des données et étude de l\'état des lieux',          'planned_start_date' => '2024-03-01', 'planned_end_date' => '2024-05-15', 'actual_start_date' => '2024-03-01', 'actual_end_date' => '2024-05-20', 'status' => 'completed'],
            ['title' => 'Élaboration de l\'Avant-Projet Détaillé (APD)',                'planned_start_date' => '2024-05-20', 'planned_end_date' => '2024-08-31', 'actual_start_date' => '2024-05-25', 'actual_end_date' => '2024-09-10', 'status' => 'completed'],
            ['title' => 'Préparation du Dossier de Consultation des Entreprises',      'planned_start_date' => '2024-09-01', 'planned_end_date' => '2024-11-30', 'actual_start_date' => '2024-09-15', 'actual_end_date' => null, 'status' => 'in_progress'],
            ['title' => 'Assistance à la passation des marchés de travaux',             'planned_start_date' => '2024-12-01', 'planned_end_date' => '2025-02-28', 'actual_start_date' => null, 'actual_end_date' => null, 'status' => 'not_started'],
            ['title' => 'Supervision et contrôle des travaux',                          'planned_start_date' => '2025-03-01', 'planned_end_date' => '2025-07-31', 'actual_start_date' => null, 'actual_end_date' => null, 'status' => 'not_started'],
        ];
        foreach ($activities1 as $a) {
            ProjectActivity::firstOrCreate(
                ['project_id' => $project1->id, 'title' => $a['title']],
                array_merge($a, ['project_id' => $project1->id, 'responsible_user_id' => $userIngenieur?->id])
            );
        }

        // Avenant Projet 1
        ProjectAmendment::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'Avenant n°1 – Extension du délai de la phase APD'],
            [
                'project_id'        => $project1->id,
                'description'       => 'Extension du délai de la phase APD de 6 semaines en raison des difficultés d\'accès au terrain pendant la saison des pluies. Impact nul sur le budget.',
                'signature_date'    => '2024-08-20',
                'file_path'         => 'projects/amendments/avenant1-aep-grandpopo.pdf',
                'budget_impact'     => 0,
                'delay_impact_days' => 42,
                'status'            => 'signed',
            ]
        );

        // Factures Projet 1
        $invoices1 = [
            ['invoice_number' => 'FAC-2024-0001', 'issue_date' => '2024-03-15', 'due_date' => '2024-04-14', 'amount' => 28_500_000, 'paid_amount' => 28_500_000, 'status' => 'paid'],
            ['invoice_number' => 'FAC-2024-0002', 'issue_date' => '2024-06-01', 'due_date' => '2024-06-30', 'amount' => 42_750_000, 'paid_amount' => 42_750_000, 'status' => 'paid'],
            ['invoice_number' => 'FAC-2024-0003', 'issue_date' => '2024-09-30', 'due_date' => '2024-10-30', 'amount' => 57_000_000, 'paid_amount' => 28_500_000, 'status' => 'sent'],
        ];
        foreach ($invoices1 as $inv) {
            ProjectInvoice::firstOrCreate(
                ['invoice_number' => $inv['invoice_number']],
                array_merge($inv, ['project_id' => $project1->id])
            );
        }

        // Risques Projet 1
        ProjectRisk::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'Retard dans les validations client'],
            [
                'project_id'      => $project1->id,
                'description'     => 'L\'ANEA présente des délais de validation des livrables supérieurs aux délais contractuels, ce qui risque de comprimer les phases suivantes.',
                'probability'     => 'high',
                'impact'          => 'medium',
                'mitigation_plan' => 'Mise en place d\'une procédure de relance formelle à J+14 après dépôt de chaque livrable. Implication du Directeur de Projet dans les échanges institutionnels.',
                'status'          => 'identified',
            ]
        );
        ProjectRisk::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'Problèmes d\'accès aux sites en saison des pluies'],
            [
                'project_id'      => $project1->id,
                'description'     => 'Les pistes rurales deviennent impraticables de juin à septembre, limitant l\'accès aux forages et aux zones d\'intervention.',
                'probability'     => 'medium',
                'impact'          => 'medium',
                'mitigation_plan' => 'Planification des visites terrain en dehors de la saison pluvieuse. Location de véhicules 4x4 adaptés.',
                'status'          => 'mitigated',
            ]
        );

        // Rapport Projet 1
        ProjectReport::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'Rapport trimestriel de suivi T3 2024'],
            [
                'project_id'  => $project1->id,
                'report_date' => '2024-09-30',
                'summary'     => 'À fin septembre 2024, la mission affiche un taux d\'avancement global de 62,5%. Les phases d\'état des lieux et d\'APD sont entièrement finalisées. La préparation du DCE est en cours.',
                'achievements'=> "Phase APD complète déposée et validée par l'ANEA. Enquêtes socio-économiques réalisées auprès de 1 200 ménages. Étude d'impact environnemental soumise à validation.",
                'next_steps'  => "Finalisation du DCE avant fin novembre. Lancement de la consultation des entreprises de travaux. Recrutement de l'ingénieur résident.",
                'issues'      => 'Délai de validation de l\'APD par l\'ANEA a dépassé de 15 jours le délai contractuel. Avenant n°1 signé pour régulariser.',
                'file_path'   => 'projects/reports/rapport-t3-2024-aep-grandpopo.pdf',
                'created_by'  => $userChefProj?->id,
            ]
        );

        // ══════════════════════════════════════════════════════════════
        // PROJET 2 – Contrôle Route Porto-Novo/Kraké (terminé, 100%)
        // ══════════════════════════════════════════════════════════════
        $project2 = Project::firstOrCreate(
            ['code' => 'BERD-2022-003'],
            [
                'title'                    => 'Mission de contrôle des travaux de bitumage RN1 Porto-Novo – Kraké',
                'offer_id'                 => $offer2?->id,
                'client_id'                => $mtpt?->id,
                'country'                  => 'Bénin',
                'status'                   => 'completed',
                'execution_percentage'     => 100.00,
                'description'              => 'Mission de contrôle et de surveillance des travaux de réhabilitation et de bitumage de la route nationale n°1 tronçon Porto-Novo – Kraké (45 km), financée par la Banque Mondiale dans le cadre du PTMF.',
                'planned_start_date'       => '2022-09-01',
                'planned_end_date'         => '2023-12-31',
                'actual_start_date'        => '2022-09-01',
                'actual_end_date'          => '2023-12-20',
                'total_budget'             => 180_000_000,
                'consumed_budget'          => 172_800_000,
                'project_manager_user_id'  => $userDirEtudes?->id,
                'project_manager_expert_id'=> $expertKpossou?->id,
            ]
        );

        // Livrables Projet 2 (tous validés)
        $deliverables2 = [
            ['title' => 'Rapport de démarrage et plan de contrôle qualité',   'planned_date' => '2022-10-01', 'invoice_amount' => 18_000_000],
            ['title' => 'Rapport semestriel de supervision – S1 2023',         'planned_date' => '2023-06-30', 'invoice_amount' => 36_000_000],
            ['title' => 'Rapport semestriel de supervision – S2 2023',         'planned_date' => '2023-12-15', 'invoice_amount' => 36_000_000],
            ['title' => 'Rapport de fin de mission et procès-verbal de réception provisoire', 'planned_date' => '2023-12-31', 'invoice_amount' => 72_000_000, 'is_milestone' => true],
        ];
        foreach ($deliverables2 as $d) {
            ProjectDeliverable::firstOrCreate(
                ['project_id' => $project2->id, 'title' => $d['title']],
                array_merge($d, [
                    'project_id'          => $project2->id,
                    'status'              => 'validated',
                    'actual_date'         => now()->subYear()->format('Y-m-d'),
                    'file_path'           => 'projects/deliverables/' . \Str::uuid() . '.pdf',
                    'validated_by'        => $userDG?->id,
                    'validated_at'        => now()->subMonths(rand(1, 12)),
                    'validation_comments' => 'Validé sans réserve par le MTPT.',
                    'is_milestone'        => $d['is_milestone'] ?? false,
                ])
            );
        }

        // Contrats experts Projet 2
        if ($expertKpossou) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project2->id, 'expert_id' => $expertKpossou->id],
                [
                    'role'         => 'Expert International Chef de Mission – Génie Civil',
                    'start_date'   => '2022-09-01',
                    'end_date'     => '2023-12-31',
                    'daily_rate'   => 450_000,
                    'planned_days' => 120,
                    'status'       => 'completed',
                ]
            );
        }
        if ($expertGbaguidi) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project2->id, 'expert_id' => $expertGbaguidi->id],
                [
                    'role'         => 'Expert Topographe – Ingénieur Résident',
                    'start_date'   => '2022-09-01',
                    'end_date'     => '2023-12-31',
                    'daily_rate'   => 200_000,
                    'planned_days' => 300,
                    'status'       => 'completed',
                ]
            );
        }

        // Factures Projet 2 (toutes payées)
        $invoices2 = [
            ['invoice_number' => 'FAC-2022-0007', 'issue_date' => '2022-10-15', 'due_date' => '2022-11-14', 'amount' => 18_000_000],
            ['invoice_number' => 'FAC-2023-0003', 'issue_date' => '2023-07-10', 'due_date' => '2023-08-09', 'amount' => 36_000_000],
            ['invoice_number' => 'FAC-2023-0011', 'issue_date' => '2023-12-20', 'due_date' => '2024-01-19', 'amount' => 36_000_000],
            ['invoice_number' => 'FAC-2024-0005', 'issue_date' => '2024-01-20', 'due_date' => '2024-02-19', 'amount' => 72_000_000],
        ];
        foreach ($invoices2 as $inv) {
            ProjectInvoice::firstOrCreate(
                ['invoice_number' => $inv['invoice_number']],
                array_merge($inv, [
                    'project_id'  => $project2->id,
                    'paid_amount' => $inv['amount'],
                    'status'      => 'paid',
                    'file_path'   => 'projects/invoices/' . \Str::uuid() . '.pdf',
                ])
            );
        }

        // Rapport Projet 2
        ProjectReport::firstOrCreate(
            ['project_id' => $project2->id, 'title' => 'Rapport de fin de mission – Route RN1 PN/Kraké'],
            [
                'project_id'  => $project2->id,
                'report_date' => '2024-01-15',
                'summary'     => 'Mission de contrôle de travaux menée à terme avec succès. Les 45 km de route bitumée ont été réceptionnés provisoirement en décembre 2023. Le taux de conformité des ouvrages aux spécifications techniques est de 98,2%.',
                'achievements'=> "Réception provisoire signée le 20/12/2023. 45 km de route bitumée. 8 ouvrages hydrauliques construits. Aucun accident du travail grave enregistré.",
                'next_steps'  => 'Suivi de la période de garantie (12 mois). Préparation de la réception définitive prévue en décembre 2024.',
                'issues'      => 'Quelques malfaçons mineures sur le revêtement entre les PK 32 et 37 ont été signalées et corrigées avant la réception.',
                'file_path'   => 'projects/reports/rapport-final-rn1-pn-krake.pdf',
                'created_by'  => $userDirEtudes?->id,
            ]
        );

        // ══════════════════════════════════════════════════════════════
        // PROJET 3 – Irrigation Zou (en préparation)
        // ══════════════════════════════════════════════════════════════
        $project3 = Project::firstOrCreate(
            ['code' => 'BERD-2024-002'],
            [
                'title'                    => 'Études de faisabilité du projet d\'irrigation de la vallée du Zou',
                'offer_id'                 => $offer3?->id,
                'client_id'                => $pnud?->id,
                'country'                  => 'Bénin',
                'status'                   => 'preparation',
                'execution_percentage'     => 0,
                'description'              => 'Mission d\'études de faisabilité technico-économique, environnementale et sociale pour la mise en valeur agricole des périmètres irrigués dans la vallée du Zou (département du Zou). Surface cible : 2 500 hectares irrigables.',
                'planned_start_date'       => now()->addMonths(2)->format('Y-m-d'),
                'planned_end_date'         => now()->addMonths(14)->format('Y-m-d'),
                'actual_start_date'        => null,
                'actual_end_date'          => null,
                'total_budget'             => 75_000_000,
                'consumed_budget'          => 0,
                'project_manager_user_id'  => $userChefProj?->id,
                'project_manager_expert_id'=> $expertDiallo?->id,
            ]
        );

        // Livrables Projet 3 (tous en attente)
        $deliverables3 = [
            ['title' => 'Rapport de démarrage de la mission d\'études',               'planned_date' => now()->addMonths(3)->format('Y-m-d'),  'invoice_amount' => 7_500_000,  'is_milestone' => true],
            ['title' => 'Étude hydrologique et hydrogéologique de la vallée du Zou', 'planned_date' => now()->addMonths(6)->format('Y-m-d'),  'invoice_amount' => 15_000_000, 'is_milestone' => false],
            ['title' => 'Étude de faisabilité technique des aménagements',            'planned_date' => now()->addMonths(9)->format('Y-m-d'),  'invoice_amount' => 18_750_000, 'is_milestone' => true],
            ['title' => 'Étude d\'impact environnemental et social',                  'planned_date' => now()->addMonths(11)->format('Y-m-d'), 'invoice_amount' => 11_250_000, 'is_milestone' => false],
            ['title' => 'Rapport de faisabilité final et plan de financement',        'planned_date' => now()->addMonths(14)->format('Y-m-d'), 'invoice_amount' => 22_500_000, 'is_milestone' => true],
        ];
        foreach ($deliverables3 as $d) {
            ProjectDeliverable::firstOrCreate(
                ['project_id' => $project3->id, 'title' => $d['title']],
                array_merge($d, ['project_id' => $project3->id, 'status' => 'pending', 'is_milestone' => $d['is_milestone'] ?? false])
            );
        }

        // Contrats experts Projet 3 (brouillons)
        if ($expertDiallo) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project3->id, 'expert_id' => $expertDiallo->id],
                [
                    'role'         => 'Expert en Hydraulique – Chef de Mission',
                    'start_date'   => now()->addMonths(2)->format('Y-m-d'),
                    'end_date'     => now()->addMonths(14)->format('Y-m-d'),
                    'daily_rate'   => 300_000,
                    'planned_days' => 90,
                    'status'       => 'draft',
                ]
            );
        }
        if ($expertAguessy) {
            ProjectExpertContract::firstOrCreate(
                ['project_id' => $project3->id, 'expert_id' => $expertAguessy->id],
                [
                    'role'         => 'Expert Agronome',
                    'start_date'   => now()->addMonths(3)->format('Y-m-d'),
                    'end_date'     => now()->addMonths(12)->format('Y-m-d'),
                    'daily_rate'   => 200_000,
                    'planned_days' => 60,
                    'status'       => 'draft',
                ]
            );
        }

        // Activités Projet 3
        $activities3 = [
            ['title' => 'Signature du contrat et mobilisation de l\'équipe',   'planned_start_date' => now()->addMonths(2)->format('Y-m-d'), 'planned_end_date' => now()->addMonths(2)->addWeeks(2)->format('Y-m-d'), 'status' => 'not_started'],
            ['title' => 'Collecte des données et descentes de terrain',         'planned_start_date' => now()->addMonths(2)->addWeeks(2)->format('Y-m-d'), 'planned_end_date' => now()->addMonths(5)->format('Y-m-d'), 'status' => 'not_started'],
        ];
        foreach ($activities3 as $a) {
            ProjectActivity::firstOrCreate(
                ['project_id' => $project3->id, 'title' => $a['title']],
                array_merge($a, [
                    'project_id'        => $project3->id,
                    'actual_start_date' => null,
                    'actual_end_date'   => null,
                    'responsible_user_id' => $userIngHydro?->id,
                ])
            );
        }

        // Risque Projet 3
        ProjectRisk::firstOrCreate(
            ['project_id' => $project3->id, 'title' => 'Disponibilité des données hydrologiques historiques'],
            [
                'project_id'      => $project3->id,
                'description'     => 'Les données hydrologiques sur le fleuve Zou peuvent être incomplètes ou non disponibles auprès de la Direction Générale de l\'Eau.',
                'probability'     => 'medium',
                'impact'          => 'high',
                'mitigation_plan' => 'Prise de contact préalable avec la DG-Eau pour vérifier la disponibilité des données. Prévoir des mesures in-situ si nécessaire.',
                'status'          => 'identified',
            ]
        );

        // Projets aléatoires supplémentaires avec sous-données
        Project::factory(4)->create()->each(function (Project $proj) {
            ProjectDeliverable::factory(rand(2, 4))->create(['project_id' => $proj->id]);
            ProjectActivity::factory(rand(2, 3))->create(['project_id' => $proj->id]);
            ProjectRisk::factory(rand(1, 2))->create(['project_id' => $proj->id]);
        });

        $this->command->info('Projets créés : 3 nominatifs complets + 4 aléatoires.');
    }
}

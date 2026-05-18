<?php

namespace Database\Seeders;

use App\Models\SecureView;
use Illuminate\Database\Seeder;

class SecureViewSeeder extends Seeder
{
    public function run(): void
    {
        $notes = [
            [
                'titre'       => 'Bienvenue sur le CRM de BERD Engineering',
                'description' => '<p>Bienvenue sur la plateforme de gestion des activités de <strong>BERD Engineering SARL</strong>.</p><p>Cet outil centralise la gestion de nos clients, partenaires, experts, manifestations d\'intérêt, offres et projets. Pour toute assistance, contactez l\'administrateur système à l\'adresse <strong>admin@berd.bj</strong>.</p>',
                'auteur'      => 'Administrateur BERD',
                'date'        => now()->subMonths(6),
                'type'        => 'info',
            ],
            [
                'titre'       => 'IMPORTANT – Renouvellement des documents administratifs',
                'description' => '<p>Attention : les documents administratifs suivants arrivent à expiration dans moins de <strong>6 mois</strong> :</p><ul><li>Attestation de Régularité Fiscale (exp. dans 8 mois)</li><li>Police d\'Assurance RC Professionnelle (exp. dans 6 mois)</li><li>Attestation OBSS (exp. dans 3 mois)</li></ul><p>Merci de prendre contact avec la DAF pour le renouvellement de ces documents avant toute soumission d\'offre.</p>',
                'auteur'      => 'Rosine KOSSOU – DAF',
                'date'        => now()->subWeeks(2),
                'type'        => 'warning',
            ],
            [
                'titre'       => 'Réunion de coordination des projets – 25 avril 2026',
                'description' => '<p>Une réunion de coordination interne est programmée le <strong>vendredi 25 avril 2026 à 10h00</strong> au siège de BERD Engineering.</p><p><strong>Ordre du jour :</strong></p><ol><li>Point d\'avancement Projet AEP Grand-Popo (BERD-2024-001)</li><li>Préparation de la soumission pour l\'AMI Irrigation Zou</li><li>Présentation des nouvelles opportunités commerciales T2 2026</li><li>Divers</li></ol><p>Présence obligatoire pour tous les chefs de projet et ingénieurs d\'études.</p>',
                'auteur'      => 'Serge AGOSSOU – Directeur Général',
                'date'        => now()->subDays(3),
                'type'        => 'info',
            ],
            [
                'titre'       => 'Félicitations – Contrat signé pour l\'AEP de Grand-Popo !',
                'description' => '<p>Nous avons le plaisir d\'annoncer que <strong>BERD Engineering</strong> a été retenu, en groupement avec ARTELIA Bénin, pour la mission d\'études et supervision du Projet AEP de la commune de <strong>Grand-Popo</strong>.</p><p>Montant du marché : <strong>285 000 000 FCFA TTC</strong><br>Durée : 18 mois<br>Chef de mission : Fatoumata DIALLO</p><p>Félicitations à toute l\'équipe mobilisée pour la préparation de cette offre !</p>',
                'auteur'      => 'Serge AGOSSOU – Directeur Général',
                'date'        => now()->subMonths(14),
                'type'        => 'success',
            ],
            [
                'titre'       => 'Procédure de sécurité informatique – Mise à jour des mots de passe',
                'description' => '<p>Dans le cadre de la politique de sécurité informatique de BERD Engineering, tous les utilisateurs sont priés de :</p><ul><li>Mettre à jour leur mot de passe sur la plateforme CRM avant le 30 avril 2026</li><li>Utiliser un mot de passe d\'au moins 12 caractères comprenant majuscules, minuscules, chiffres et caractères spéciaux</li><li>Ne pas partager leurs identifiants de connexion</li></ul><p>En cas de difficulté, contacter l\'administrateur système.</p>',
                'auteur'      => 'Administrateur BERD',
                'date'        => now()->subWeeks(1),
                'type'        => 'danger',
            ],
        ];

        foreach ($notes as $data) {
            SecureView::firstOrCreate(['titre' => $data['titre']], $data);
        }

        $this->command->info('Notes de tableau de bord créées : ' . count($notes) . '.');
    }
}

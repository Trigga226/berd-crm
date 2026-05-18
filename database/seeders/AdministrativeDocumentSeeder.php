<?php

namespace Database\Seeders;

use App\Models\AdministrativeDocument;
use Illuminate\Database\Seeder;

class AdministrativeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'title'           => 'Registre du Commerce et du Crédit Mobilier (RCCM)',
                'category'        => 'RCCM',
                'file_path'       => 'administrative/berd-rccm.pdf',
                'expiration_date' => null,
                'description'     => 'RCCM n° RB/COT/18-B-4987 délivré par le Tribunal de Commerce de Cotonou.',
                'additional_info' => ['numero' => 'RB/COT/18-B-4987', 'tribunal' => 'Tribunal de Commerce de Cotonou'],
            ],
            [
                'title'           => 'Numéro d\'Identification Fiscale Unique (IFU)',
                'category'        => 'Fiscalité',
                'file_path'       => 'administrative/berd-ifu.pdf',
                'expiration_date' => null,
                'description'     => 'Attestation IFU n° 3201800987654 délivrée par la Direction des Impôts.',
                'additional_info' => ['ifu' => '3201800987654'],
            ],
            [
                'title'           => 'Statuts de la Société BERD Engineering SARL',
                'category'        => 'Status',
                'file_path'       => 'administrative/berd-statuts.pdf',
                'expiration_date' => null,
                'description'     => 'Statuts constitutifs de BERD Engineering SARL au capital de 10 000 000 FCFA, déposés en 2018.',
            ],
            [
                'title'           => 'Attestation de Régularité Fiscale',
                'category'        => 'Fiscalité',
                'file_path'       => 'administrative/berd-regularite-fiscale.pdf',
                'expiration_date' => now()->addMonths(8)->format('Y-m-d'),
                'description'     => 'Attestation de régularité fiscale délivrée par la Direction Générale des Impôts du Bénin.',
            ],
            [
                'title'           => 'Police d\'Assurance Responsabilité Civile Professionnelle',
                'category'        => 'Assurances',
                'file_path'       => 'administrative/berd-assurance-rc-pro.pdf',
                'expiration_date' => now()->addMonths(6)->format('Y-m-d'),
                'description'     => 'Police n° RC-PRO-2024-0542 souscrite auprès de NSIA Assurances Bénin.',
                'additional_info' => ['assureur' => 'NSIA Assurances Bénin', 'police' => 'RC-PRO-2024-0542'],
            ],
            [
                'title'           => 'Attestation de l\'OBSS – Régularité à la Sécurité Sociale',
                'category'        => 'Fiscalité',
                'file_path'       => 'administrative/berd-obss.pdf',
                'expiration_date' => now()->addMonths(3)->format('Y-m-d'),
                'description'     => 'Attestation de bonne situation vis-à-vis de l\'Office Béninois de Sécurité Sociale.',
            ],
            [
                'title'           => 'Liste des Références Techniques Majeures – BERD Engineering',
                'category'        => 'References',
                'file_path'       => 'administrative/berd-references-techniques.pdf',
                'expiration_date' => null,
                'description'     => 'Document récapitulatif des 25 principales missions réalisées depuis la création du bureau.',
            ],
            [
                'title'           => 'Agrément Technique auprès de l\'ARCEP Bénin',
                'category'        => 'Divers',
                'file_path'       => 'administrative/berd-agrement-arcep.pdf',
                'expiration_date' => now()->addMonths(18)->format('Y-m-d'),
                'description'     => 'Agrément pour la réalisation d\'études télécom et de câblage réseau.',
            ],
        ];

        foreach ($documents as $data) {
            AdministrativeDocument::firstOrCreate(['title' => $data['title']], $data);
        }

        $this->command->info('Documents administratifs créés : ' . count($documents) . '.');
    }
}

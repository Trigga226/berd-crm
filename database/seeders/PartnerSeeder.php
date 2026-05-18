<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\PartnerDocument;
use App\Models\PartnerReference;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            [
                'partner' => [
                    'type'          => 'morale',
                    'name'          => 'EGIS International Bénin',
                    'ifu'           => '3202000234567',
                    'email'         => 'contact@egis-benin.com',
                    'phone'         => '+229 21 30 11 00',
                    'address'       => 'Immeuble COVED, Rue du Commerce',
                    'city'          => 'Cotonou',
                    'country'       => 'Bénin',
                    'website'       => 'www.egis.fr',
                    'domains'       => ['Génie Civil', 'BTP', 'Hydraulique', 'Topographie', 'VRD'],
                    'contact_name'  => 'M. Pierre LANGLOIS',
                    'contact_email' => 'p.langlois@egis.fr',
                    'contact_phone' => '+229 97 11 00 01',
                    'notes'         => 'Filiale locale du groupe EGIS. Partenaire stratégique en ingénierie de transport et d\'eau.',
                ],
                'documents' => [
                    ['title' => 'Registre de Commerce (RCCM)', 'file_path' => 'partners/documents/egis-rccm.pdf', 'expiration_date' => null],
                    ['title' => 'Attestation fiscale', 'file_path' => 'partners/documents/egis-attestation-fiscale.pdf', 'expiration_date' => now()->addMonths(10)->format('Y-m-d')],
                ],
                'references' => [
                    [
                        'title'       => 'Supervision des travaux de construction de la route Cotonou-Porto-Novo',
                        'client_name' => 'Ministère des Travaux Publics et des Transports',
                        'description' => 'Mission de contrôle de travaux sur 36 km de route bitumée, incluant ouvrages d\'art et aménagements connexes.',
                        'domains'     => ['Génie Civil', 'BTP'],
                        'year'        => '2021',
                    ],
                ],
            ],
            [
                'partner' => [
                    'type'          => 'morale',
                    'name'          => 'Louis Berger Group Afrique de l\'Ouest',
                    'ifu'           => null,
                    'email'         => 'contact.ao@louisberger.com',
                    'phone'         => '+225 27 20 22 30 00',
                    'address'       => 'Cocody Riviera Golf, Avenue Chardy',
                    'city'          => 'Abidjan',
                    'country'       => 'Côte d\'Ivoire',
                    'website'       => 'www.louisberger.com',
                    'domains'       => ['Génie Civil', 'Gestion de Projet', 'Infrastructure', 'Hydraulique'],
                    'contact_name'  => 'Mme. Nadia ASSIRI',
                    'contact_email' => 'n.assiri@louisberger.com',
                    'contact_phone' => '+225 07 22 30 00',
                    'notes'         => 'Firma internationale d\'ingénierie et de conseil, très active en Afrique subsaharienne.',
                ],
                'documents' => [
                    ['title' => 'Profil de l\'entreprise', 'file_path' => 'partners/documents/lbg-profil.pdf', 'expiration_date' => null],
                ],
                'references' => [
                    [
                        'title'       => 'Étude de faisabilité du projet d\'irrigation du fleuve Niger',
                        'client_name' => 'BOAD',
                        'description' => 'Études techniques et environnementales pour la mise en valeur agricole de 5 000 ha dans la vallée du Niger.',
                        'domains'     => ['Hydraulique', 'Agriculture'],
                        'year'        => '2020',
                    ],
                    [
                        'title'       => 'Mission d\'assistance technique à la SONEB',
                        'client_name' => 'SONEB Bénin',
                        'description' => 'Appui institutionnel et technique pour l\'amélioration des performances de la distribution d\'eau potable.',
                        'domains'     => ['Hydraulique', 'Gestion de Projet'],
                        'year'        => '2022',
                    ],
                ],
            ],
            [
                'partner' => [
                    'type'          => 'morale',
                    'name'          => 'ARTELIA Bénin',
                    'ifu'           => '3201800345678',
                    'email'         => 'benin@artelia.com',
                    'phone'         => '+229 21 30 44 55',
                    'address'       => 'Lot 789, Quartier Haie Vive',
                    'city'          => 'Cotonou',
                    'country'       => 'Bénin',
                    'website'       => 'www.artelia.com',
                    'domains'       => ['Hydraulique', 'Génie Civil', 'Environnement', 'Architecture'],
                    'contact_name'  => 'M. Frédéric DUCHESNE',
                    'contact_email' => 'f.duchesne@artelia.com',
                    'contact_phone' => '+229 96 44 55 66',
                    'notes'         => 'Groupe d\'ingénierie français présent localement. Spécialiste eau, énergie et ville durable.',
                ],
                'documents' => [
                    ['title' => 'Registre de Commerce (RCCM)', 'file_path' => 'partners/documents/artelia-rccm.pdf', 'expiration_date' => null],
                    ['title' => 'Police d\'assurance RC', 'file_path' => 'partners/documents/artelia-assurance.pdf', 'expiration_date' => now()->addYear()->format('Y-m-d')],
                ],
                'references' => [
                    [
                        'title'       => 'Maîtrise d\'œuvre pour la construction de l\'usine de traitement des eaux de Godomey',
                        'client_name' => 'SONEB',
                        'description' => 'Conception et supervision de la construction d\'une station de traitement d\'eau potable de 30 000 m³/j.',
                        'domains'     => ['Hydraulique', 'Génie Civil'],
                        'year'        => '2019',
                    ],
                ],
            ],
            [
                'partner' => [
                    'type'          => 'morale',
                    'name'          => 'SOGEA-SATOM Bénin',
                    'ifu'           => '3200500456789',
                    'email'         => 'contact@sogea-satom.bj',
                    'phone'         => '+229 21 33 10 20',
                    'address'       => 'Zone Industrielle de Cotonou',
                    'city'          => 'Cotonou',
                    'country'       => 'Bénin',
                    'website'       => 'www.sogea-satom.com',
                    'domains'       => ['BTP', 'Génie Civil', 'VRD', 'Architecture'],
                    'contact_name'  => 'M. Thomas GIRARD',
                    'contact_email' => 't.girard@sogea-satom.com',
                    'contact_phone' => '+229 97 10 20 30',
                    'notes'         => 'Grande entreprise de construction, partenaire idéal pour les projets BTP de grande envergure.',
                ],
                'documents' => [
                    ['title' => 'Statuts de la société', 'file_path' => 'partners/documents/sogea-statuts.pdf', 'expiration_date' => null],
                ],
                'references' => [
                    [
                        'title'       => 'Construction du Terminal à Conteneurs du Port de Cotonou',
                        'client_name' => 'Port Autonome de Cotonou',
                        'description' => 'Travaux de génie civil pour l\'extension du terminal à conteneurs, 120 000 m² de surface totale.',
                        'domains'     => ['Génie Civil', 'BTP'],
                        'year'        => '2018',
                    ],
                ],
            ],
            [
                'partner' => [
                    'type'          => 'morale',
                    'name'          => 'Africonsult Architectes Ingénieurs',
                    'ifu'           => null,
                    'email'         => 'contact@africonsult.sn',
                    'phone'         => '+221 33 821 45 67',
                    'address'       => 'Point E, Rue Aimé Césaire',
                    'city'          => 'Dakar',
                    'country'       => 'Sénégal',
                    'website'       => 'www.africonsult.sn',
                    'domains'       => ['Architecture', 'Génie Civil', 'Urbanisme', 'BTP'],
                    'contact_name'  => 'Arch. Cheikh NDIAYE',
                    'contact_email' => 'c.ndiaye@africonsult.sn',
                    'contact_phone' => '+221 77 821 45 67',
                ],
                'documents' => [],
                'references' => [
                    [
                        'title'       => 'Études architecturales et techniques du Centre Culturel de Dakar',
                        'client_name' => 'Ministère de la Culture du Sénégal',
                        'description' => 'Conception architecturale et études techniques d\'exécution pour un centre culturel de 8 000 m².',
                        'domains'     => ['Architecture', 'Génie Civil'],
                        'year'        => '2022',
                    ],
                ],
            ],
            [
                'partner' => [
                    'type'          => 'morale',
                    'name'          => 'Tractebel Engineering Afrique',
                    'ifu'           => null,
                    'email'         => 'afrique@tractebel.com',
                    'phone'         => '+225 27 22 40 01 00',
                    'address'       => 'Plateau, Avenue Lamblin',
                    'city'          => 'Abidjan',
                    'country'       => 'Côte d\'Ivoire',
                    'website'       => 'www.tractebel.com',
                    'domains'       => ['Energie Renouvelable', 'Génie Électrique', 'Hydraulique', 'Mines et Géologie'],
                    'contact_name'  => 'M. Romain BERTRAND',
                    'contact_email' => 'r.bertrand@tractebel.com',
                    'contact_phone' => '+225 07 22 40 01',
                    'notes'         => 'Spécialiste des projets énergie et infrastructures critiques en Afrique.',
                ],
                'documents' => [],
                'references' => [
                    [
                        'title'       => 'Étude de faisabilité du parc solaire photovoltaïque de Natitingou',
                        'client_name' => 'Ministère de l\'Energie du Bénin',
                        'description' => 'Études techniques, économiques et environnementales pour un parc solaire de 20 MWc.',
                        'domains'     => ['Energie Renouvelable', 'Génie Électrique'],
                        'year'        => '2023',
                    ],
                ],
            ],
        ];

        foreach ($partners as $data) {
            $partner = Partner::firstOrCreate(
                ['name' => $data['partner']['name']],
                $data['partner'],
            );

            foreach ($data['documents'] as $doc) {
                PartnerDocument::firstOrCreate(
                    ['partner_id' => $partner->id, 'title' => $doc['title']],
                    array_merge($doc, ['partner_id' => $partner->id]),
                );
            }

            foreach ($data['references'] as $ref) {
                PartnerReference::firstOrCreate(
                    ['partner_id' => $partner->id, 'title' => $ref['title']],
                    array_merge($ref, ['partner_id' => $partner->id]),
                );
            }
        }

        // Partenaires aléatoires supplémentaires
        Partner::factory(4)->morale()->create();

        $this->command->info('Partenaires créés : ' . count($partners) . ' nominatifs + 4 aléatoires.');
    }
}

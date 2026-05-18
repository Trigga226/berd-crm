<?php

namespace Database\Seeders;

use App\Models\Expert;
use Illuminate\Database\Seeder;

class ExpertSeeder extends Seeder
{
    public function run(): void
    {
        $experts = [
            [
                'first_name'          => 'Emmanuel',
                'last_name'           => 'KPOSSOU',
                'email'               => 'e.kpossou@berd.bj',
                'phone'               => '+229 97 10 11 12',
                'years_of_experience' => 20,
                'skills'              => ['Génie Civil', 'BTP', 'VRD', 'Topographie', 'Gestion de Projet', 'AutoCAD'],
                'formations'          => [[
                    'diplome'       => "Ingénieur d'État",
                    'specialite'    => 'Génie Civil',
                    'etablissement' => 'EPAC/UAC (Bénin)',
                    'annee'         => 2004,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert International Chef de Mission',
                    'entreprise'  => 'Bureau BERD Engineering',
                    'debut'       => 2010,
                    'fin'         => now()->year,
                    'description' => 'Pilotage de missions d\'études et de supervision dans le secteur des infrastructures.',
                ]],
            ],
            [
                'first_name'          => 'Fatoumata',
                'last_name'           => 'DIALLO',
                'email'               => 'f.diallo.expert@gmail.com',
                'phone'               => '+229 96 20 21 22',
                'years_of_experience' => 14,
                'skills'              => ['Hydraulique', 'HEC-RAS', 'EPANET', 'Environnement', 'SIG', 'ArcGIS'],
                'formations'          => [[
                    'diplome'       => 'Master 2',
                    'specialite'    => 'Hydraulique et Gestion des Ressources en Eau',
                    'etablissement' => '2iE Ouagadougou (Burkina Faso)',
                    'annee'         => 2010,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert en Hydraulique Urbaine et Rurale',
                    'entreprise'  => 'SOGEA-SATOM Afrique de l\'Ouest',
                    'debut'       => 2011,
                    'fin'         => 2018,
                    'description' => 'Études hydrauliques pour des projets AEP et d\'assainissement dans 5 pays d\'Afrique de l\'Ouest.',
                ]],
            ],
            [
                'first_name'          => 'Thierry',
                'last_name'           => 'GBAGUIDI',
                'email'               => 'thierry.gbaguidi@yahoo.fr',
                'phone'               => '+229 97 30 31 32',
                'years_of_experience' => 10,
                'skills'              => ['Topographie', 'SIG', 'ArcGIS', 'AutoCAD', 'GPS/GNSS', 'Géotechnique'],
                'formations'          => [[
                    'diplome'       => "Ingénieur d'État",
                    'specialite'    => 'Topographie et Géomatique',
                    'etablissement' => 'EPAC/UAC (Bénin)',
                    'annee'         => 2014,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert Topographe',
                    'entreprise'  => 'Bureau BERD Engineering',
                    'debut'       => 2015,
                    'fin'         => now()->year,
                    'description' => 'Levés topographiques, cartographie SIG et contrôle géométrique des ouvrages.',
                ]],
            ],
            [
                'first_name'          => 'Ama',
                'last_name'           => 'DODJI',
                'email'               => 'a.dodji.env@gmail.com',
                'phone'               => '+229 96 40 41 42',
                'years_of_experience' => 12,
                'skills'              => ['Environnement', 'Étude d\'Impact Environnemental', 'SIG', 'Agriculture', 'Suivi-Évaluation'],
                'formations'          => [[
                    'diplome'       => 'Master 2',
                    'specialite'    => 'Sciences de l\'Environnement et Gestion Durable',
                    'etablissement' => 'Université d\'Abomey-Calavi (Bénin)',
                    'annee'         => 2012,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert Environnementaliste',
                    'entreprise'  => 'Consultance indépendante',
                    'debut'       => 2013,
                    'fin'         => now()->year,
                    'description' => 'Réalisation d\'études d\'impact environnemental et social pour de grands projets d\'infrastructure.',
                ]],
            ],
            [
                'first_name'          => 'Ibrahim',
                'last_name'           => 'MAIGA',
                'email'               => 'i.maiga.elec@gmail.com',
                'phone'               => '+229 97 50 51 52',
                'years_of_experience' => 16,
                'skills'              => ['Génie Électrique', 'Energie Renouvelable', 'Automatisme / Instrumentation', 'QHSE', 'Gestion de Projet'],
                'formations'          => [[
                    'diplome'       => "Ingénieur d'État",
                    'specialite'    => 'Génie Électrique et Energétique',
                    'etablissement' => 'INPHB Yamoussoukro (Côte d\'Ivoire)',
                    'annee'         => 2008,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert en Génie Électrique et Energies Renouvelables',
                    'entreprise'  => 'Tractebel Engineering Afrique',
                    'debut'       => 2009,
                    'fin'         => 2020,
                    'description' => 'Études de faisabilité et supervision de projets d\'électrification rurale et de parcs solaires.',
                ]],
            ],
            [
                'first_name'          => 'Sylvie',
                'last_name'           => 'HOUENOUKPO',
                'email'               => 's.houenoukpo@outlook.com',
                'phone'               => '+229 96 60 61 62',
                'years_of_experience' => 8,
                'skills'              => ['Suivi-Évaluation', 'Gestion de Projet', 'Passation de Marchés', 'Analyse Financière'],
                'formations'          => [[
                    'diplome'       => 'Master 2',
                    'specialite'    => 'Management de Projets de Développement',
                    'etablissement' => 'Université Paris-Est Créteil (France)',
                    'annee'         => 2016,
                ]],
                'experiences'         => [[
                    'poste'       => 'Experte en Suivi-Évaluation',
                    'entreprise'  => 'PNUD Bénin',
                    'debut'       => 2017,
                    'fin'         => 2022,
                    'description' => 'Mise en place de systèmes de suivi-évaluation pour des programmes de développement.',
                ]],
            ],
            [
                'first_name'          => 'Jean-Marc',
                'last_name'           => 'AKOWANOU',
                'email'               => 'jm.akowanou@gmail.com',
                'phone'               => '+229 97 70 71 72',
                'years_of_experience' => 13,
                'skills'              => ['Génie Mécanique', 'Maintenance Industrielle', 'QHSE', 'Génie des Procédés', 'Génie Industriel'],
                'formations'          => [[
                    'diplome'       => "Ingénieur d'État",
                    'specialite'    => 'Génie Mécanique et Industriel',
                    'etablissement' => 'EPAC/UAC (Bénin)',
                    'annee'         => 2011,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert en Génie Mécanique',
                    'entreprise'  => 'OCBN – Organisation Commune Bénin-Niger',
                    'debut'       => 2012,
                    'fin'         => now()->year,
                    'description' => 'Maintenance et inspection des équipements mécaniques ferroviaires et industriels.',
                ]],
            ],
            [
                'first_name'          => 'Chantal',
                'last_name'           => 'NZINGA',
                'email'               => 'c.nzinga.arch@gmail.com',
                'phone'               => '+229 96 80 81 82',
                'years_of_experience' => 11,
                'skills'              => ['Architecture', 'Urbanisme', 'AutoCAD', 'BIM/Revit', 'Génie Civil'],
                'formations'          => [[
                    'diplome'       => 'Diplôme d\'Architecte DPLG',
                    'specialite'    => 'Architecture et Urbanisme',
                    'etablissement' => 'École Polytechnique de Thiès (Sénégal)',
                    'annee'         => 2013,
                ]],
                'experiences'         => [[
                    'poste'       => 'Architecte Urbaniste Consultante',
                    'entreprise'  => 'Africonsult Architectes Ingénieurs',
                    'debut'       => 2014,
                    'fin'         => 2023,
                    'description' => 'Conception architecturale et schémas directeurs d\'aménagement urbain.',
                ]],
            ],
            [
                'first_name'          => 'Blessing',
                'last_name'           => 'AGUESSY',
                'email'               => 'b.aguessy@berd.bj',
                'phone'               => '+229 97 90 91 92',
                'years_of_experience' => 7,
                'skills'              => ['Agriculture', 'Hydraulique', 'Environnement', 'SIG', 'Suivi-Évaluation'],
                'formations'          => [[
                    'diplome'       => 'Master 2',
                    'specialite'    => 'Agronomie et Développement Rural',
                    'etablissement' => 'Université de Parakou (Bénin)',
                    'annee'         => 2017,
                ]],
                'experiences'         => [[
                    'poste'       => 'Expert Agronome',
                    'entreprise'  => 'FAO Bénin',
                    'debut'       => 2018,
                    'fin'         => now()->year,
                    'description' => 'Appui technique aux projets d\'irrigation et de développement agricole.',
                ]],
            ],
            [
                'first_name'          => 'Kouassi',
                'last_name'           => 'ASSOUMOU',
                'email'               => 'k.assoumou.gc@gmail.com',
                'phone'               => '+229 96 00 01 02',
                'years_of_experience' => 18,
                'skills'              => ['Génie Civil', 'Géotechnique', 'Topographie', 'BTP', 'Gestion de Projet', 'AutoCAD'],
                'formations'          => [[
                    'diplome'       => "Ingénieur d'État",
                    'specialite'    => 'Génie Civil et Géotechnique',
                    'etablissement' => 'INSA Lyon (France)',
                    'annee'         => 2006,
                ]],
                'experiences'         => [[
                    'poste'       => 'Ingénieur Résident Senior',
                    'entreprise'  => 'EGIS International',
                    'debut'       => 2007,
                    'fin'         => 2022,
                    'description' => 'Supervision résidente de travaux de génie civil et d\'ouvrages d\'art sur plusieurs grands projets.',
                ]],
            ],
        ];

        foreach ($experts as $data) {
            Expert::firstOrCreate(['email' => $data['email']], $data);
        }

        // Experts aléatoires supplémentaires
        Expert::factory(5)->create();

        $this->command->info('Experts créés : ' . count($experts) . ' nominatifs + 5 aléatoires.');
    }
}

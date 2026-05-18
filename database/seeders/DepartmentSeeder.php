<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Poste;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            [
                'name'        => 'Direction Générale',
                'description' => 'Pilotage stratégique, représentation institutionnelle et coordination générale de BERD Engineering.',
                'postes'      => [
                    ['title' => 'Directeur Général',            'description' => 'Responsable de la stratégie, de la gestion globale et de la représentation externe de la société.'],
                    ['title' => 'Directeur Général Adjoint',    'description' => 'Assiste le DG dans la coordination des activités et le remplace en cas d\'absence.'],
                    ['title' => 'Assistante de Direction',      'description' => 'Gestion de l\'agenda, du courrier et de la logistique de la direction générale.'],
                ],
            ],
            [
                'name'        => 'Bureau d\'Études Techniques',
                'description' => 'Conception, études et expertise technique dans les domaines du génie civil, hydraulique, environnement et topographie.',
                'postes'      => [
                    ['title' => 'Directeur des Études',            'description' => 'Supervision et validation de l\'ensemble des études techniques produites par le bureau.'],
                    ['title' => 'Ingénieur Principal Génie Civil', 'description' => 'Référent technique pour les études de structures et d\'infrastructure.'],
                    ['title' => 'Ingénieur Études Hydraulique',    'description' => 'Spécialiste en hydraulique urbaine, assainissement et ressources en eau.'],
                    ['title' => 'Dessinateur Projeteur CAO/DAO',   'description' => 'Production des plans techniques sous AutoCAD et logiciels de conception.'],
                ],
            ],
            [
                'name'        => 'Gestion de Projets',
                'description' => 'Planification, suivi, contrôle et reporting des projets en cours d\'exécution.',
                'postes'      => [
                    ['title' => 'Directeur de Projets',          'description' => 'Coordination globale du portefeuille de projets et interface avec les clients.'],
                    ['title' => 'Chef de Projet Senior',         'description' => 'Pilotage opérationnel des projets complexes, gestion des équipes et des délais.'],
                    ['title' => 'Coordinateur de Projet',        'description' => 'Suivi quotidien de l\'avancement des activités et coordination des parties prenantes.'],
                    ['title' => 'Contrôleur de Gestion Projet',  'description' => 'Suivi budgétaire, analyse des écarts et reporting financier des projets.'],
                ],
            ],
            [
                'name'        => 'Partenariats & Développement Commercial',
                'description' => 'Identification des opportunités d\'affaires, gestion des partenariats stratégiques et réponse aux appels d\'offres.',
                'postes'      => [
                    ['title' => 'Responsable Partenariats & Business Development', 'description' => 'Développement du réseau de partenaires et identification de nouvelles opportunités commerciales.'],
                    ['title' => 'Chargé d\'Affaires',                              'description' => 'Montage des dossiers de réponse aux appels d\'offres et suivi des manifestations d\'intérêt.'],
                ],
            ],
            [
                'name'        => 'Administratif & Financier',
                'description' => 'Gestion comptable, administrative, des ressources humaines et des obligations légales et fiscales.',
                'postes'      => [
                    ['title' => 'Directeur Administratif & Financier', 'description' => 'Pilotage de la fonction financière, RH et administrative de la société.'],
                    ['title' => 'Comptable Principal',                  'description' => 'Tenue de la comptabilité générale et analytique, déclarations fiscales et sociales.'],
                    ['title' => 'Assistante Administrative',            'description' => 'Gestion du courrier, de l\'archivage et du support administratif quotidien.'],
                    ['title' => 'Gestionnaire des Ressources Humaines', 'description' => 'Administration du personnel, paie, formation et relations sociales.'],
                ],
            ],
        ];

        foreach ($structure as $deptData) {
            $postes = $deptData['postes'];
            unset($deptData['postes']);

            $department = Department::firstOrCreate(['name' => $deptData['name']], $deptData);

            foreach ($postes as $posteData) {
                Poste::firstOrCreate(
                    ['title' => $posteData['title'], 'department_id' => $department->id],
                    array_merge($posteData, ['department_id' => $department->id]),
                );
            }
        }

        $this->command->info('Départements et postes créés : ' . count($structure) . ' départements.');
    }
}

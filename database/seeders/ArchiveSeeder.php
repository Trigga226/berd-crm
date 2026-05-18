<?php

namespace Database\Seeders;

use App\Models\Archive;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArchiveSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail   = 'admin@berd.bj';
        $chefEmail    = 'chef.projets@berd.bj';
        $dirEtudesEmail = 'dir.etudes@berd.bj';

        $archives = [
            [
                'titre'         => 'AMI – Réhabilitation réseau d\'eau Abomey-Calavi (2021)',
                'type'          => 'Avis de manifestation',
                'fichier'       => [
                    'archives/ami-2021/ami-aep-abomey-calavi.pdf',
                    'archives/ami-2021/reponse-berd-ami-aep-abomey.pdf',
                ],
                'date_archive'  => '2022-01-15',
                'archive_par'   => $adminEmail,
                'observation'   => 'AMI de 2021 archivé après clôture du processus. BERD n\'avait pas été sélectionné pour la liste restreinte.',
                'resultat'      => 'Non retenu pour la liste restreinte',
            ],
            [
                'titre'         => 'Manifestation d\'intérêt – Études ferroviaires OCBN 2020',
                'type'          => 'Manifestation',
                'fichier'       => [
                    'archives/manifestation-2020/dossier-mi-ocbn.pdf',
                    'archives/manifestation-2020/cvs-experts-ocbn.pdf',
                    'archives/manifestation-2020/references-berd.pdf',
                ],
                'date_archive'  => '2021-03-10',
                'archive_par'   => $chefEmail,
                'observation'   => 'Manifestation déposée pour le compte de l\'OCBN. Sélectionné sur liste restreinte mais offre finale non soumise faute de partenaire financier.',
                'resultat'      => 'Liste restreinte – Offre finale non déposée',
            ],
            [
                'titre'         => 'Offre technique et financière – Construction du Centre de Santé de Glazoué',
                'type'          => 'Offres',
                'fichier'       => [
                    'archives/offres-2022/offre-technique-glazoue.pdf',
                    'archives/offres-2022/offre-financiere-glazoue.pdf',
                ],
                'date_archive'  => '2022-07-20',
                'archive_par'   => $dirEtudesEmail,
                'observation'   => 'Offre soumise en groupement avec ARTELIA. Perdant face à un concurrent proposant un prix 18% inférieur.',
                'resultat'      => 'Non retenu – Offre financière hors budget',
            ],
            [
                'titre'         => 'Livrables finaux – Projet Contrôle Route RN1 Porto-Novo/Kraké (BERD-2022-003)',
                'type'          => 'Livrable',
                'fichier'       => [
                    'archives/projet-berd-2022-003/rapport-final.pdf',
                    'archives/projet-berd-2022-003/pv-reception-provisoire.pdf',
                    'archives/projet-berd-2022-003/photos-travaux.zip',
                ],
                'date_archive'  => '2024-02-05',
                'archive_par'   => $chefEmail,
                'observation'   => 'Archivage complet du projet BERD-2022-003, entièrement clôturé. Tous les livrables et PV sont archivés.',
                'resultat'      => 'Projet clôturé avec succès – Réception provisoire délivrée',
            ],
            [
                'titre'         => 'Rapports de supervision trimestriels – Projet AEP Natitingou 2019-2020',
                'type'          => 'Rapport',
                'fichier'       => [
                    'archives/aep-natitingou/rapport-t1-2020.pdf',
                    'archives/aep-natitingou/rapport-t2-2020.pdf',
                    'archives/aep-natitingou/rapport-final-natitingou.pdf',
                ],
                'date_archive'  => '2021-01-20',
                'archive_par'   => $dirEtudesEmail,
                'observation'   => 'Projet AEP Natitingou entièrement finalisé et réceptionné. Documents archivés pour la constitution du dossier de références.',
                'resultat'      => 'Mission réussie – Certifiée par l\'ANEA',
            ],
            [
                'titre'         => 'Documents administratifs BERD – Archives 2020',
                'type'          => 'Document administratif',
                'fichier'       => [
                    'archives/admin-2020/rccm-2018.pdf',
                    'archives/admin-2020/attestation-fiscale-2020.pdf',
                    'archives/admin-2020/assurance-rc-pro-2020.pdf',
                ],
                'date_archive'  => '2021-01-05',
                'archive_par'   => $adminEmail,
                'observation'   => 'Archivage annuel des documents administratifs expirés. Documents remplacés par les versions 2021.',
                'resultat'      => null,
            ],
        ];

        foreach ($archives as $data) {
            Archive::firstOrCreate(['titre' => $data['titre']], $data);
        }

        $this->command->info('Archives créées : ' . count($archives) . '.');
    }
}

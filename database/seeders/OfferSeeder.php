<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\FinancialOffer;
use App\Models\Offer;
use App\Models\Partner;
use App\Models\TechnicalOffer;
use App\Models\User;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $anea   = Client::where('name', 'like', '%Agence Nationale de l\'Eau%')->first();
        $mtpt   = Client::where('name', 'like', '%Ministère des Travaux Publics%')->first();
        $pnud   = Client::where('name', 'like', '%Nations Unies%')->first();
        $boad   = Client::where('name', 'like', '%Banque Ouest Africaine%')->first();

        $artelia  = Partner::where('name', 'ARTELIA Bénin')->first();
        $lbg      = Partner::where('name', 'like', '%Louis Berger%')->first();

        $userDirEtudes = User::where('email', 'dir.etudes@berd.bj')->first();
        $userChefProj  = User::where('email', 'chef.projets@berd.bj')->first();
        $userIngenieur = User::where('email', 'k.akpovi@berd.bj')->first();

        // ──────────────────────────────────────────────────────────────
        // OFFRE 1 – AEP Grand-Popo (liée à la manifestation gagnée → donnera un projet)
        // ──────────────────────────────────────────────────────────────
        $offer1 = Offer::firstOrCreate(
            ['title' => 'Études et supervision AEP de Grand-Popo'],
            [
                'client_id'       => $anea?->id,
                'country'         => 'Bénin',
                'result'          => 'Gagné',
                'submission_mode' => 'physical',
                'is_consortium'   => true,
            ]
        );
        if ($artelia)       $offer1->partners()->syncWithoutDetaching([$artelia->id  => ['is_lead' => true]]);
        if ($userDirEtudes) $offer1->users()->syncWithoutDetaching([$userDirEtudes->id => ['role' => 'charge_etude']]);
        if ($userChefProj)  $offer1->users()->syncWithoutDetaching([$userChefProj->id  => ['role' => 'assistant']]);

        TechnicalOffer::firstOrCreate(
            ['offer_id' => $offer1->id],
            [
                'title'                => 'Offre Technique – AEP Grand-Popo',
                'description'          => 'Offre technique détaillant la méthodologie, le planning et les CV des experts proposés.',
                'note'                 => '87.50',
                'result'               => 'Retenu',
                'deadline'             => now()->subMonths(13),
                'internal_control_date'=> now()->subMonths(13)->subDays(7),
                'submission_mode'      => 'physical',
                'submission_date'      => now()->subMonths(13)->format('Y-m-d H:i:s'),
            ]
        );
        FinancialOffer::firstOrCreate(
            ['offer_id' => $offer1->id],
            [
                'title'                => 'Offre Financière – AEP Grand-Popo',
                'description'          => 'Proposition financière globale pour la mission en groupement avec ARTELIA Bénin.',
                'note'                 => '90.00',
                'result'               => 'Retenu',
                'deadline'             => now()->subMonths(13),
                'submission_mode'      => 'physical',
                'submission_date'      => now()->subMonths(13)->format('Y-m-d H:i:s'),
            ]
        );

        // ──────────────────────────────────────────────────────────────
        // OFFRE 2 – Contrôle Route Porto-Novo/Kraké (liée → projet terminé)
        // ──────────────────────────────────────────────────────────────
        $offer2 = Offer::firstOrCreate(
            ['title' => 'Mission de contrôle des travaux – Route Porto-Novo/Kraké'],
            [
                'client_id'       => $mtpt?->id,
                'country'         => 'Bénin',
                'result'          => 'Gagné',
                'submission_mode' => 'physical',
                'is_consortium'   => false,
            ]
        );
        if ($userDirEtudes) $offer2->users()->syncWithoutDetaching([$userDirEtudes->id => ['role' => 'charge_etude']]);
        if ($userIngenieur) $offer2->users()->syncWithoutDetaching([$userIngenieur->id  => ['role' => 'assistant']]);

        TechnicalOffer::firstOrCreate(
            ['offer_id' => $offer2->id],
            [
                'title'           => 'Offre Technique – Contrôle Route PN/Kraké',
                'note'            => '92.25',
                'result'          => 'Retenu',
                'deadline'        => now()->subMonths(21),
                'submission_mode' => 'physical',
                'submission_date' => now()->subMonths(21)->format('Y-m-d H:i:s'),
            ]
        );
        FinancialOffer::firstOrCreate(
            ['offer_id' => $offer2->id],
            [
                'title'           => 'Offre Financière – Contrôle Route PN/Kraké',
                'note'            => '88.00',
                'result'          => 'Retenu',
                'deadline'        => now()->subMonths(21),
                'submission_mode' => 'physical',
                'submission_date' => now()->subMonths(21)->format('Y-m-d H:i:s'),
            ]
        );

        // ──────────────────────────────────────────────────────────────
        // OFFRE 3 – Irrigation Zou (en cours de préparation → donnera un projet)
        // ──────────────────────────────────────────────────────────────
        $offer3 = Offer::firstOrCreate(
            ['title' => 'Études de faisabilité – Irrigation vallée du Zou'],
            [
                'client_id'       => $pnud?->id,
                'country'         => 'Bénin',
                'result'          => 'En attente',
                'submission_mode' => 'online',
                'is_consortium'   => false,
            ]
        );
        if ($userDirEtudes) $offer3->users()->syncWithoutDetaching([$userDirEtudes->id => ['role' => 'charge_etude']]);
        if ($userChefProj)  $offer3->users()->syncWithoutDetaching([$userChefProj->id  => ['role' => 'assistant']]);

        TechnicalOffer::firstOrCreate(
            ['offer_id' => $offer3->id],
            [
                'title'                => 'Offre Technique – Études Irrigation Zou',
                'deadline'             => now()->addMonths(4),
                'internal_control_date'=> now()->addMonths(3),
                'submission_mode'      => 'online',
            ]
        );
        FinancialOffer::firstOrCreate(
            ['offer_id' => $offer3->id],
            [
                'title'           => 'Offre Financière – Études Irrigation Zou',
                'deadline'        => now()->addMonths(4),
                'submission_mode' => 'online',
            ]
        );

        // ──────────────────────────────────────────────────────────────
        // OFFRE 4 – Électrification Nord-Bénin (soumise)
        // ──────────────────────────────────────────────────────────────
        $offer4 = Offer::firstOrCreate(
            ['title' => 'Étude de faisabilité – Électrification rurale Nord-Bénin'],
            [
                'client_id'       => $boad?->id,
                'country'         => 'Bénin',
                'result'          => 'En attente',
                'submission_mode' => 'email',
                'is_consortium'   => true,
            ]
        );
        if ($lbg)           $offer4->partners()->syncWithoutDetaching([$lbg->id       => ['is_lead' => false]]);
        if ($userDirEtudes) $offer4->users()->syncWithoutDetaching([$userDirEtudes->id => ['role' => 'charge_etude']]);

        TechnicalOffer::firstOrCreate(
            ['offer_id' => $offer4->id],
            [
                'title'           => 'Offre Technique – Électrification Nord-Bénin',
                'deadline'        => now()->addMonths(2),
                'submission_mode' => 'email',
            ]
        );

        $this->command->info('Offres créées : 4 nominatives avec offres techniques et financières.');
    }
}

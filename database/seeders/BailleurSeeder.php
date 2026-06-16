<?php

namespace Database\Seeders;

use App\Models\Bailleur;
use Illuminate\Database\Seeder;

class BailleurSeeder extends Seeder
{
    public function run(): void
    {
        $bailleurs = [
            ['acronym' => 'BAD',  'name' => 'Banque Africaine de Développement',       'type' => 'multilateral', 'country' => 'Côte d\'Ivoire', 'website' => 'afdb.org'],
            ['acronym' => 'BM',   'name' => 'Banque Mondiale',                          'type' => 'multilateral', 'country' => 'États-Unis',     'website' => 'banquemondiale.org'],
            ['acronym' => 'BOAD', 'name' => 'Banque Ouest Africaine de Développement',  'type' => 'multilateral', 'country' => 'Togo',           'website' => 'boad.org'],
            ['acronym' => 'UE',   'name' => 'Union Européenne',                         'type' => 'multilateral', 'country' => 'Belgique',       'website' => 'europa.eu'],
            ['acronym' => 'AFD',  'name' => 'Agence Française de Développement',        'type' => 'bilateral',    'country' => 'France',         'website' => 'afd.fr'],
            ['acronym' => 'BID',  'name' => 'Banque Islamique de Développement',        'type' => 'multilateral', 'country' => 'Arabie Saoudite','website' => 'isdb.org'],
            ['acronym' => 'BADEA','name' => 'Banque Arabe pour le Développement Économique en Afrique', 'type' => 'multilateral', 'country' => 'Soudan', 'website' => 'badea.org'],
            ['acronym' => 'KfW',  'name' => 'Kreditanstalt für Wiederaufbau',           'type' => 'bilateral',    'country' => 'Allemagne',      'website' => 'kfw.de'],
            ['acronym' => 'PNUD', 'name' => 'Programme des Nations Unies pour le Développement', 'type' => 'multilateral', 'country' => 'États-Unis', 'website' => 'undp.org'],
            ['acronym' => 'FAD',  'name' => 'Fonds Africain de Développement',          'type' => 'multilateral', 'country' => 'Côte d\'Ivoire', 'website' => 'afdb.org'],
        ];

        foreach ($bailleurs as $data) {
            Bailleur::firstOrCreate(
                ['acronym' => $data['acronym']],
                array_merge($data, [
                    'domains' => ['Infrastructures', 'Eau & Assainissement', 'Énergie'],
                ])
            );
        }
    }
}

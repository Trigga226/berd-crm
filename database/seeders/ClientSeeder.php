<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            // ─── Institutions publiques ───
            [
                'type'          => 'morale',
                'name'          => 'Ministère des Travaux Publics et des Transports',
                'ifu'           => null,
                'email'         => 'courrier@mtpt.gouv.bj',
                'phone'         => '+229 21 30 00 01',
                'address'       => 'Route de l\'Aéroport, Quartier Cadjehoun',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'contact_name'  => 'M. Olivier EDAH',
                'contact_email' => 'direction@mtpt.gouv.bj',
                'contact_phone' => '+229 97 10 00 01',
                'notes'         => 'Ministère tutelle pour les infrastructures routières et de transport.',
            ],
            [
                'type'          => 'morale',
                'name'          => 'Agence Nationale de l\'Eau et de l\'Assainissement (ANEA)',
                'ifu'           => null,
                'email'         => 'info@anea.bj',
                'phone'         => '+229 21 30 12 45',
                'address'       => 'Lot 2547, Zone Industrielle',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'contact_name'  => 'Mme. Rose KOSSOU',
                'contact_email' => 'dg@anea.bj',
                'contact_phone' => '+229 97 20 02 02',
                'notes'         => 'Agence publique en charge des projets d\'eau potable et d\'assainissement.',
            ],
            [
                'type'          => 'morale',
                'name'          => 'Banque Ouest Africaine de Développement (BOAD)',
                'ifu'           => null,
                'email'         => 'direction@boad.org',
                'phone'         => '+228 22 21 59 06',
                'address'       => '68, Avenue de la Libération',
                'city'          => 'Lomé',
                'country'       => 'Togo',
                'website'       => 'www.boad.org',
                'contact_name'  => 'M. Serge AGBEYOME',
                'contact_email' => 's.agbeyome@boad.org',
                'contact_phone' => '+228 90 10 20 30',
                'notes'         => 'Institution multilatérale de financement du développement en Afrique de l\'Ouest.',
            ],
            [
                'type'          => 'morale',
                'name'          => 'Programme des Nations Unies pour le Développement – Bénin',
                'ifu'           => null,
                'email'         => 'registry.bj@undp.org',
                'phone'         => '+229 21 30 97 00',
                'address'       => 'Maison des Nations Unies, Zone des Ambassades',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'website'       => 'www.undp.org/benin',
                'contact_name'  => 'Dr. Aminata TOURÉ',
                'contact_email' => 'a.toure@undp.org',
                'contact_phone' => '+229 96 30 97 10',
                'notes'         => 'Bureau pays du PNUD – principal partenaire sur les projets de développement durable.',
            ],
            [
                'type'          => 'morale',
                'name'          => 'Mairie de Cotonou',
                'ifu'           => null,
                'email'         => 'mairie@mairie-cotonou.bj',
                'phone'         => '+229 21 31 56 23',
                'address'       => 'Boulevard Saint-Michel',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'contact_name'  => 'M. Luc ATTINDEHOU',
                'contact_email' => 'sg@mairie-cotonou.bj',
                'contact_phone' => '+229 97 56 23 00',
            ],
            [
                'type'          => 'morale',
                'name'          => 'Direction Générale de l\'Eau (DG-Eau)',
                'ifu'           => null,
                'email'         => 'contact@dgeau.bj',
                'phone'         => '+229 21 30 08 14',
                'address'       => 'Ancien Immeuble ONAB, Quartier Gbégamey',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'contact_name'  => 'Ing. Barnabé HOUNTO',
                'contact_email' => 'b.hounto@dgeau.bj',
                'contact_phone' => '+229 97 08 14 00',
                'notes'         => 'Service technique du Ministère en charge de l\'Eau.',
            ],
            // ─── Secteur privé ───
            [
                'type'          => 'morale',
                'name'          => 'Société Béninoise d\'Energie Electrique (SBEE) SA',
                'ifu'           => '3201900123456',
                'email'         => 'info@sbee.bj',
                'phone'         => '+229 21 31 25 09',
                'address'       => 'Boulevard de la Marina',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'website'       => 'www.sbee.bj',
                'contact_name'  => 'M. Pascal HOUMEY',
                'contact_email' => 'dg@sbee.bj',
                'contact_phone' => '+229 97 25 09 01',
                'notes'         => 'Société nationale de distribution d\'électricité au Bénin.',
            ],
            [
                'type'          => 'morale',
                'name'          => 'Organisation Commune Bénin-Niger (OCBN)',
                'ifu'           => '3200800045678',
                'email'         => 'contact@ocbn.bj',
                'phone'         => '+229 21 31 50 14',
                'address'       => 'Gare de Cotonou',
                'city'          => 'Cotonou',
                'country'       => 'Bénin',
                'contact_name'  => 'M. Ibrahim DAOURA',
                'contact_email' => 'dg@ocbn.bj',
                'contact_phone' => '+229 97 50 14 00',
            ],
            // ─── Personnes physiques ───
            [
                'type'       => 'physique',
                'name'       => 'HOUNGNIKPO',
                'first_name' => 'Jean-Baptiste',
                'email'      => 'jb.houngnikpo@gmail.com',
                'phone'      => '+229 97 12 34 56',
                'address'    => 'Quartier Zongo',
                'city'       => 'Parakou',
                'country'    => 'Bénin',
                'notes'      => 'Promoteur immobilier indépendant, projet de résidence à Parakou.',
            ],
            [
                'type'       => 'physique',
                'name'       => 'ELEGBE',
                'first_name' => 'Marie Pauline',
                'email'      => 'mp.elegbe@outlook.com',
                'phone'      => '+229 96 78 90 12',
                'address'    => 'Lot 145, Cité Houéyiho',
                'city'       => 'Cotonou',
                'country'    => 'Bénin',
            ],
        ];

        foreach ($clients as $data) {
            Client::firstOrCreate(['name' => $data['name'], 'type' => $data['type']], $data);
        }

        // Clients aléatoires supplémentaires
        Client::factory(8)->create();

        $this->command->info('Clients créés : ' . count($clients) . ' nominatifs + 8 aléatoires.');
    }
}

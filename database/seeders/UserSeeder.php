<?php

namespace Database\Seeders;

use App\Models\Poste;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les postes clés créés par DepartmentSeeder
        $posteDG          = Poste::where('title', 'Directeur Général')->first();
        $posteDirEtudes   = Poste::where('title', 'Directeur des Études')->first();
        $posteChefProjet  = Poste::where('title', 'Chef de Projet Senior')->first();
        $posteIngenieur   = Poste::where('title', 'Ingénieur Principal Génie Civil')->first();
        $posteIngHydro    = Poste::where('title', 'Ingénieur Études Hydraulique')->first();
        $postePartenariat = Poste::where('title', 'Responsable Partenariats & Business Development')->first();
        $posteDAF         = Poste::where('title', 'Directeur Administratif & Financier')->first();
        $posteChargAff    = Poste::where('title', 'Chargé d\'Affaires')->first();

        $users = [
            // ─── Super Administrateur système ───
            [
                'name'     => 'Administrateur BERD',
                'email'    => 'admin@berd.bj',
                'password' => Hash::make('Admin@2024!'),
                'poste_id' => null,
                'num_poste'=> '+229 21 30 00 00',
                'role'     => 'super_admin',
            ],
            // ─── Direction Générale ───
            [
                'name'     => 'Serge AGOSSOU',
                'email'    => 'dg@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteDG?->id,
                'num_poste'=> '+229 21 30 00 01',
                'num_perso'=> '+229 97 10 20 30',
                'role'     => 'admin',
            ],
            // ─── Bureau d'Études ───
            [
                'name'     => 'Aïssatou DIALLO',
                'email'    => 'dir.etudes@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteDirEtudes?->id,
                'num_poste'=> '+229 21 30 00 02',
                'num_perso'=> '+229 96 40 50 60',
                'role'     => 'manager',
            ],
            [
                'name'     => 'Kouassi AKPOVI',
                'email'    => 'k.akpovi@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteIngenieur?->id,
                'num_poste'=> '+229 21 30 00 03',
                'num_perso'=> '+229 97 55 66 77',
                'role'     => 'collaborateur',
            ],
            [
                'name'     => 'Fatoumata COULIBALY',
                'email'    => 'f.coulibaly@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteIngHydro?->id,
                'num_poste'=> '+229 21 30 00 04',
                'num_perso'=> '+229 96 11 22 33',
                'role'     => 'collaborateur',
            ],
            // ─── Gestion de Projets ───
            [
                'name'     => 'Abdoulaye TRAORE',
                'email'    => 'chef.projets@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteChefProjet?->id,
                'num_poste'=> '+229 21 30 00 05',
                'num_perso'=> '+229 97 33 44 55',
                'role'     => 'manager',
            ],
            // ─── Partenariats ───
            [
                'name'     => 'Chantal NZINGA',
                'email'    => 'partenariats@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $postePartenariat?->id,
                'num_poste'=> '+229 21 30 00 06',
                'num_perso'=> '+229 96 77 88 99',
                'role'     => 'manager',
            ],
            [
                'name'     => 'Brice HOUNKPATIN',
                'email'    => 'b.hounkpatin@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteChargAff?->id,
                'num_poste'=> '+229 21 30 00 07',
                'num_perso'=> '+229 97 22 33 44',
                'role'     => 'collaborateur',
            ],
            // ─── Administratif & Financier ───
            [
                'name'     => 'Rosine KOSSOU',
                'email'    => 'daf@berd.bj',
                'password' => Hash::make('password'),
                'poste_id' => $posteDAF?->id,
                'num_poste'=> '+229 21 30 00 08',
                'num_perso'=> '+229 96 00 11 22',
                'role'     => 'admin',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::firstOrCreate(['email' => $data['email']], array_merge($data, [
                'email_verified_at' => now(),
            ]));

            $user->assignRole($role);
        }

        // Utilisateurs aléatoires supplémentaires avec le rôle collaborateur
        User::factory(5)->create()->each(fn ($u) => $u->assignRole('collaborateur'));

        $this->command->info('Utilisateurs créés : ' . count($users) . ' comptes nominatifs + 5 aléatoires.');
    }
}

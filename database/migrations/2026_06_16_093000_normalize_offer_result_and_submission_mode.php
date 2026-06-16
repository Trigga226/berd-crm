<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalise les valeurs legacy des champs contraints (result / submission_mode)
     * des offres afin qu'elles correspondent aux options des Select Filament et ne
     * fassent plus échouer la règle de validation « in ».
     *
     *  - chaîne vide ('')  → null   (état « En cours » / non renseigné)
     *  - 'Retenu'          → 'won'  (ancien libellé équivalent à « Gagné »)
     */
    public function up(): void
    {
        $tables = ['offers', 'technical_offers', 'financial_offers'];

        foreach ($tables as $table) {
            // '' → null pour les deux colonnes contraintes
            DB::table($table)->where('result', '')->update(['result' => null]);
            DB::table($table)->where('submission_mode', '')->update(['submission_mode' => null]);

            // Ancien libellé 'Retenu' → 'won'
            DB::table($table)->where('result', 'Retenu')->update(['result' => 'won']);
        }
    }

    public function down(): void
    {
        // Normalisation non réversible : les valeurs d'origine ('', 'Retenu')
        // étaient invalides et ne sont volontairement pas restaurées.
    }
};

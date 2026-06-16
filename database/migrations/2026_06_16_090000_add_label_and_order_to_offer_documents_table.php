<?php

use App\Support\OfferDocumentSections;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la personnalisation par offre des pièces technique/financière :
     *  - category : discriminant 'technical' | 'financial'
     *  - label    : intitulé renommable affiché et utilisé à la génération
     *  - sort_order : ordre de fusion des fichiers dans le PDF compilé
     *
     * Le champ `type` devient optionnel (les pièces ajoutées librement n'ont pas
     * de clé technique figée) ; il est conservé pour la compatibilité.
     */
    public function up(): void
    {
        Schema::table('offer_documents', function (Blueprint $table) {
            $table->string('category')->nullable()->after('financial_offer_id');
            $table->string('label')->nullable()->after('type');
            $table->unsignedInteger('sort_order')->default(0)->after('label');
            $table->string('type')->nullable()->change();
        });

        $this->backfill();
    }

    /**
     * Reprise des pièces existantes : déduit la catégorie depuis le préfixe du
     * type, renseigne un intitulé par défaut et un ordre cohérent avec l'ancienne
     * composition figée.
     */
    protected function backfill(): void
    {
        $technicalOrder = array_flip(array_keys(OfferDocumentSections::technical()));
        $financialOrder = array_flip(array_keys(OfferDocumentSections::financial()));

        DB::table('offer_documents')->orderBy('id')->each(function ($doc) use ($technicalOrder, $financialOrder) {
            $type = $doc->type;

            if ($type === null) {
                return;
            }

            $isTechnical = str_starts_with($type, 'tech_');
            $isFinancial = str_starts_with($type, 'fine_');

            if (! $isTechnical && ! $isFinancial) {
                return;
            }

            $category = $isTechnical ? 'technical' : 'financial';
            $order    = $isTechnical
                ? ($technicalOrder[$type] ?? 999)
                : ($financialOrder[$type] ?? 999);

            DB::table('offer_documents')
                ->where('id', $doc->id)
                ->update([
                    'category'   => $category,
                    'label'      => OfferDocumentSections::labelFor($type) ?? $type,
                    'sort_order' => $order,
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('offer_documents', function (Blueprint $table) {
            $table->dropColumn(['category', 'label', 'sort_order']);
            // `type` est laissé nullable : la valeur d'origine n'est pas restaurée
            // pour éviter d'échouer sur d'éventuelles pièces sans clé technique.
        });
    }
};

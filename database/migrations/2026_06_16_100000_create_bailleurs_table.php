<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bailleurs de fonds (institutions de financement) souvent associés aux projets :
     * banques de développement, agences bilatérales, fondations, fonds nationaux, etc.
     */
    public function up(): void
    {
        Schema::create('bailleurs', function (Blueprint $table) {
            $table->id();

            // Catégorie de bailleur (multilatéral, bilatéral, national, fondation, privé...)
            $table->string('type')->default('multilateral');

            $table->string('name');         // Dénomination complète
            $table->string('acronym')->nullable(); // Sigle (BAD, BM, BOAD, UE, AFD...)

            // Coordonnées
            $table->string('ifu')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();

            // Domaines / secteurs financés (JSON)
            $table->json('domains')->nullable();

            // Contact principal
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bailleurs');
    }
};

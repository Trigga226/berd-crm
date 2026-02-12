<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('physique'); // physique / morale
            $table->string('name'); // Nom (Physique) ou Raison Sociale (Morale)
            $table->string('first_name')->nullable(); // Prénom (Physique uniquement)
            $table->string('ifu')->nullable(); // Identifiant Fiscal Unique (Morale)

            // Coordonnées Entité
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();

            // Contact Principal (Pour Entités Morales)
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

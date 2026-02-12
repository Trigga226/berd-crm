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
        Schema::create('partner_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Nom du projet/ref
            $table->string('client_name')->nullable();
            $table->text('description')->nullable();
            $table->json('domains')->nullable(); // Domaines concernés par cette ref
            $table->year('year')->nullable();
            $table->string('file_path')->nullable(); // Attestation: public/partenaire/{slug}/references/
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_references');
    }
};

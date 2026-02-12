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
        Schema::create('avis_manifestations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('reference_number')->unique()->nullable();

            // Client: On peut lier au modèle ou mettre un texte libre pour les prospects
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name')->nullable(); // Fallback si pas encore client

            $table->dateTime('deadline');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();

            $table->string('status')->default('pending'); // pending, submitted_to_analysis, validated, rejected

            $table->date('submission_date')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        // Table Pivot pour les Chargés de Projet
        Schema::create('avis_manifestation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avis_manifestation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis_manifestations');
    }
};

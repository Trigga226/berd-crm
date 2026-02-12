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
        Schema::create('manifestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avis_manifestation_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, submitted, won, lost, abandoned
            $table->string('submission_mode')->nullable(); // online, physical, email
            $table->string('result')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->dateTime('internal_control_date')->nullable();
            $table->boolean('is_groupement')->default(false);
            $table->foreignId('lead_partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->string('country')->nullable();
            $table->string('client_name')->nullable();
            $table->string('generated_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('manifestation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role'); // charge_etude, assistant
            $table->timestamps();
        });

        Schema::create('manifestation_partner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestation_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->boolean('is_lead')->default(false);
            $table->timestamps();
        });

        Schema::create('manifestation_expert', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestation_id')->constrained()->onDelete('cascade');
            $table->foreignId('expert_id')->constrained()->onDelete('cascade');
            $table->string('cv_path')->nullable(); // Specific CV for this manifestation
            $table->timestamps();
        });

        Schema::create('manifestation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestation_id')->constrained()->onDelete('cascade');
            $table->string('type'); // page_garde, sommaire, lettre, piece_admin, presentation, adresse, reference
            $table->string('file_path');
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifestation_documents');
        Schema::dropIfExists('manifestation_expert');
        Schema::dropIfExists('manifestation_partner');
        Schema::dropIfExists('manifestation_user');
        Schema::dropIfExists('manifestations');
    }
};

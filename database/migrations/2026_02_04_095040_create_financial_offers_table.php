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
        Schema::create('financial_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->string('title')->default('Offre Financière');
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->string('result')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->dateTime('internal_control_date')->nullable();
            $table->string('submission_mode')->nullable();
            $table->dateTime('submission_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_offers');
    }
};

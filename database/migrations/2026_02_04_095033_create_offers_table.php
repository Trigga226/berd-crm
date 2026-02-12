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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('manifestation_id')->nullable()->constrained('manifestations')->nullOnDelete();
            $table->string('result')->nullable();
            $table->string('submission_mode')->nullable();
            $table->string('dp_path')->nullable();
            $table->boolean('is_consortium')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};

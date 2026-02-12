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
        Schema::create('project_expert_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expert_id')->constrained()->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->integer('planned_days')->nullable();
            $table->string('contract_path')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_expert_contracts');
    }
};

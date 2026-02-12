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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->constrained();
            $table->string('country');
            $table->enum('status', ['preparation', 'ongoing', 'suspended', 'completed', 'cancelled'])->default('preparation');
            $table->decimal('execution_percentage', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->decimal('total_budget', 15, 2)->nullable();
            $table->decimal('consumed_budget', 15, 2)->default(0);
            $table->string('contract_path')->nullable();
            $table->foreignId('project_manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('project_manager_expert_id')->nullable()->constrained('experts')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

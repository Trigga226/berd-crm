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
        // Add deleted_at to project_deliverables
        Schema::table('project_deliverables', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to project_expert_contracts
        Schema::table('project_expert_contracts', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to project_amendments
        Schema::table('project_amendments', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to project_invoices
        Schema::table('project_invoices', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to project_activities
        Schema::table('project_activities', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to project_risks
        Schema::table('project_risks', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to project_reports
        Schema::table('project_reports', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_deliverables', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_expert_contracts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_amendments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_activities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_risks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

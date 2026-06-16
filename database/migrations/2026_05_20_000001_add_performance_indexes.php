<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifestations', function (Blueprint $table) {
            $table->index(['status', 'deadline'], 'manifestations_status_deadline_idx');
            $table->index(['created_at'],          'manifestations_created_at_idx');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->index(['client_id', 'created_at'], 'offers_client_created_idx');
            $table->index(['result'],                   'offers_result_idx');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index(['status', 'planned_end_date'], 'projects_status_end_date_idx');
            $table->index(['client_id'],                  'projects_client_id_idx');
        });

        Schema::table('archives', function (Blueprint $table) {
            $table->index(['type', 'date_archive'], 'archives_type_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('manifestations', function (Blueprint $table) {
            $table->dropIndex('manifestations_status_deadline_idx');
            $table->dropIndex('manifestations_created_at_idx');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex('offers_client_created_idx');
            $table->dropIndex('offers_result_idx');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_status_end_date_idx');
            $table->dropIndex('projects_client_id_idx');
        });

        Schema::table('archives', function (Blueprint $table) {
            $table->dropIndex('archives_type_date_idx');
        });
    }
};

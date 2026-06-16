<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $indexes = [
        'CREATE INDEX IF NOT EXISTS manifestations_status_deadline_idx ON manifestations (status, deadline)',
        'CREATE INDEX IF NOT EXISTS manifestations_created_at_idx ON manifestations (created_at)',
        'CREATE INDEX IF NOT EXISTS offers_client_created_idx ON offers (client_id, created_at)',
        'CREATE INDEX IF NOT EXISTS offers_result_idx ON offers (result)',
        'CREATE INDEX IF NOT EXISTS projects_status_end_date_idx ON projects (status, planned_end_date)',
        'CREATE INDEX IF NOT EXISTS projects_client_id_idx ON projects (client_id)',
        'CREATE INDEX IF NOT EXISTS archives_type_annee_domaine_idx ON archives (type, annee, domaine)',
        'CREATE INDEX IF NOT EXISTS archives_entite_idx ON archives (entite_type, entite_id)',
        'CREATE INDEX IF NOT EXISTS archives_statut_idx ON archives (statut)',
    ];

    public function up(): void
    {
        foreach ($this->indexes as $sql) {
            DB::statement($sql);
        }
    }

    public function down(): void
    {
        $names = [
            'manifestations_status_deadline_idx',
            'manifestations_created_at_idx',
            'offers_client_created_idx',
            'offers_result_idx',
            'projects_status_end_date_idx',
            'projects_client_id_idx',
            'archives_type_annee_domaine_idx',
            'archives_entite_idx',
            'archives_statut_idx',
        ];

        foreach ($names as $name) {
            DB::statement("DROP INDEX IF EXISTS {$name}");
        }
    }
};

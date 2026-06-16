<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avis_manifestations', function (Blueprint $table) {
            $table->json('domains')->nullable()->after('description');
            $table->decimal('ai_score', 3, 1)->nullable()->after('domains');
            $table->text('ai_summary')->nullable()->after('ai_score');
        });
    }

    public function down(): void
    {
        Schema::table('avis_manifestations', function (Blueprint $table) {
            $table->dropColumn(['domains', 'ai_score', 'ai_summary']);
        });
    }
};

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
        Schema::table('manifestations', function (Blueprint $table) {
            // Rename existing 'note' (text) to 'observation' if data preservation is needed, 
            // but since it's dev/empty or I can just change it.
            // Let's drop 'note' (text) and add 'score' and 'observation'.
            $table->dropColumn('note');
            $table->double('score')->nullable()->after('result'); // Note (sur 100 par ex)
            $table->text('observation')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('manifestations', function (Blueprint $table) {
            $table->dropColumn(['score', 'observation']);
            $table->text('note')->nullable();
        });
    }
};

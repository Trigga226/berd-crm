<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experts', function (Blueprint $table) {
            // Array of {lang: 'fr'|'en'|'pt', path: '...', label: '...'} objects
            $table->json('cv_paths')->nullable()->after('cv_path');
            $table->tinyInteger('rating')->nullable()->after('years_of_experience'); // 1-5 stars
            $table->unsignedInteger('won_manifestations_count')->default(0)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->dropColumn(['cv_paths', 'rating', 'won_manifestations_count']);
        });
    }
};

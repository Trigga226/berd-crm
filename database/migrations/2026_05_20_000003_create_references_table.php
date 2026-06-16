<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('client_name');
            $table->text('description')->nullable();
            $table->json('domains');
            $table->string('country')->nullable();
            $table->unsignedSmallInteger('year');
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('active'); // active | archived
            $table->timestamps();
            $table->softDeletes();

            $table->index(['year', 'status'], 'references_year_status_idx');
            $table->index('project_id',       'references_project_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};

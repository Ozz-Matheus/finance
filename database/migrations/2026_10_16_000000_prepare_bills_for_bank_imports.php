<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table): void {
            $table->decimal('cost', 12, 2)->nullable()->change();
            $table->string('source', 100)->nullable();
            $table->string('external_id', 191)->nullable();
            $table->unique(['source', 'external_id'], 'bills_source_external_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table): void {
            $table->dropUnique('bills_source_external_id_unique');
            $table->dropColumn(['source', 'external_id']);
            $table->integer('cost')->nullable()->change();
        });
    }
};

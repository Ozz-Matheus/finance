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
        // Some existing installations already have this column, even if the
        // migration is not recorded in the migrations table.
        if (! Schema::hasColumn('bills', 'payment_method_id')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->foreignId('payment_method_id')->constrained()->after('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            //
        });
    }
};

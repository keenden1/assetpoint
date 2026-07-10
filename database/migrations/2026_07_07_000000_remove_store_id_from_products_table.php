<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Stores are a standalone list (BO branch names); products no longer
     * belong to a store.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Nullable on rollback: existing rows have no store to restore.
            $table->foreignId('store_id')->nullable()->after('id')
                ->constrained()->cascadeOnDelete();
        });
    }
};

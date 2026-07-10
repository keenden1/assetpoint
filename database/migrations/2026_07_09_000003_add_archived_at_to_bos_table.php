<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Records are archived (hidden from the main list, restorable) rather
     * than deleted.
     */
    public function up(): void
    {
        Schema::table('bos', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bos', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};

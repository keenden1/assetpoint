<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Snapshot the actor's name at the time of the action so renaming a user
     * doesn't rewrite the audit trail.
     */
    public function up(): void
    {
        Schema::table('bo_activities', function (Blueprint $table) {
            $table->string('user_name')->nullable()->after('user_id');
        });

        // Backfill existing activity rows with the actor's current name.
        DB::statement('UPDATE bo_activities JOIN users ON users.id = bo_activities.user_id SET bo_activities.user_name = users.name');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bo_activities', function (Blueprint $table) {
            $table->dropColumn('user_name');
        });
    }
};

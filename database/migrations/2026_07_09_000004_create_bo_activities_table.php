<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Audit trail per BO record: who created / edited / archived / restored
     * it, and when. user_id survives user deletion so history stays intact.
     */
    public function up(): void
    {
        Schema::create('bo_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created | updated | archived | restored
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bo_activities');
    }
};

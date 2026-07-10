<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The B.O editor works on ONE shared draft stored server-side, so several
     * users can build the same sheet together (the pages poll for changes).
     */
    public function up(): void
    {
        Schema::create('bo_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('B.O');
            $table->date('date')->nullable();
            $table->string('dr')->nullable();
            $table->string('store')->nullable();
            // Set when a saved record is loaded for editing.
            $table->foreignId('bo_id')->nullable()->constrained('bos')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bo_draft_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bo_draft_id')->constrained()->cascadeOnDelete();
            $table->string('dr')->nullable();
            $table->string('store')->nullable();
            $table->string('product');
            $table->unsignedInteger('qty');
            $table->decimal('cost', 10, 2);
            $table->string('remarks')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bo_draft_entries');
        Schema::dropIfExists('bo_drafts');
    }
};

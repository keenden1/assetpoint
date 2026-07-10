<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Saved B.O sheets: a header row (bos) plus its line items (bo_items).
     * Product name and cost are snapshotted so later price changes don't
     * rewrite history.
     */
    public function up(): void
    {
        Schema::create('bos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // B.O | PULL-OUT | RETURN
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('bo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bo_id')->constrained()->cascadeOnDelete();
            $table->string('dr')->nullable();
            $table->string('store')->nullable();
            $table->string('product');
            $table->unsignedInteger('qty');
            $table->decimal('cost', 10, 2);
            $table->decimal('total', 12, 2);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bo_items');
        Schema::dropIfExists('bos');
    }
};

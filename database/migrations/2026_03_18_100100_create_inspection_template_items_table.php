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
        Schema::create('inspection_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_template_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->text('instructions')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index(['inspection_template_id', 'sort_order'], 'iti_template_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_template_items');
    }
};

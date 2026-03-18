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
        Schema::create('inspection_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_template_item_id')->constrained()->cascadeOnDelete();
            $table->string('response', 30);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['inspection_id', 'inspection_template_item_id'], 'ir_inspection_item_unique');
            $table->index('response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_responses');
    }
};

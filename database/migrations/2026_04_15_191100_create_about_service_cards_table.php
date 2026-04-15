<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_service_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['about_section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_service_cards');
    }
};

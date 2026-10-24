<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insight_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name_en', 120);
            $table->string('name_ar', 120);
            $table->string('slug_en', 140)->unique();
            $table->string('slug_ar', 140)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_categories');
    }
};

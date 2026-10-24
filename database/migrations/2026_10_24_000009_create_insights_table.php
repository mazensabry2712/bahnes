<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insights', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('insight_category_id')->constrained('insight_categories')->restrictOnDelete();
            $table->string('title_en', 180)->nullable();
            $table->string('title_ar', 180)->nullable();
            $table->string('slug_en', 180)->nullable()->unique();
            $table->string('slug_ar', 180)->nullable()->unique();
            $table->text('excerpt_en')->nullable();
            $table->text('excerpt_ar')->nullable();
            $table->longText('body_en')->nullable();
            $table->longText('body_ar')->nullable();
            $table->string('meta_title_en', 180)->nullable();
            $table->string('meta_title_ar', 180)->nullable();
            $table->string('meta_description_en', 320)->nullable();
            $table->string('meta_description_ar', 320)->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'insight_category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};

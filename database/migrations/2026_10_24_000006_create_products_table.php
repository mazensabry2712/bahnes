<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            foreach (['title', 'slug', 'summary', 'body', 'requirements', 'license_terms', 'delivery_terms', 'support_terms', 'update_terms', 'price_note'] as $field) {
                foreach (['en', 'ar'] as $locale) {
                    $column = "{$field}_{$locale}";
                    if ($field === 'slug') {
                        $table->string($column)->nullable()->unique();
                    } elseif (in_array($field, ['title', 'price_note'], true)) {
                        $table->string($column, 180)->nullable();
                    } elseif ($field === 'summary') {
                        $table->text($column)->nullable();
                    } else {
                        $table->longText($column)->nullable();
                    }
                }
            }
            $table->string('demo_url', 2048)->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

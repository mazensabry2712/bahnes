<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('company', 180)->nullable();
            $table->string('type', 40)->index();
            $table->string('subject', 200)->nullable();
            $table->text('message');
            $table->string('preferred_locale', 2)->default('en');
            $table->string('source', 80)->default('website_contact');
            $table->string('status', 40)->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};

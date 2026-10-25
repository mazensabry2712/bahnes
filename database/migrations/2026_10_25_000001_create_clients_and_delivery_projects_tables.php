<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('preferred_locale', 5)->default('en');
            $table->text('internal_notes')->nullable();
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company', 'name']);
        });

        Schema::create('delivery_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('planning');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['client_id', 'status']);
            $table->index(['status', 'target_date']);
        });

        Schema::create('project_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_project_id')->constrained('delivery_projects')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('summary');
            $table->text('completed_work')->nullable();
            $table->text('current_work')->nullable();
            $table->text('next_step')->nullable();
            $table->date('target_date')->nullable();
            $table->boolean('client_visible')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->text('email_error')->nullable();
            $table->timestamps();
            $table->index(['delivery_project_id', 'client_visible', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_updates');
        Schema::dropIfExists('delivery_projects');
        Schema::dropIfExists('clients');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->text('agreed_services')->nullable();
            $table->text('commercial_summary')->nullable();
        });

        Schema::table('delivery_projects', function (Blueprint $table): void {
            $table->string('reference')->nullable()->unique();
            $table->text('blocker')->nullable();
            $table->text('delivery_notes')->nullable();
        });

        Schema::create('project_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_project_id')->constrained('delivery_projects')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('todo');
            $table->boolean('is_milestone')->default(false);
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['delivery_project_id', 'status', 'sort_order']);
        });

        Schema::create('client_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_name');
            $table->string('storage_path')->unique();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->string('category', 40)->default('other');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'category']);
        });

        Schema::create('project_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('delivery_project_id')->nullable()->constrained('delivery_projects')->nullOnDelete();
            $table->string('event_key', 80);
            $table->string('summary', 500);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'created_at']);
            $table->index(['delivery_project_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_activities');
        Schema::dropIfExists('client_documents');
        Schema::dropIfExists('project_tasks');

        Schema::table('delivery_projects', function (Blueprint $table): void {
            $table->dropUnique(['reference']);
            $table->dropColumn(['reference', 'blocker', 'delivery_notes']);
        });

        Schema::table('clients', function (Blueprint $table): void {
            $table->dropColumn(['agreed_services', 'commercial_summary']);
        });
    }
};

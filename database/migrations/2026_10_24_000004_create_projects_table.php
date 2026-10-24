<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('slug_en')->nullable()->unique();
            $table->string('slug_ar')->nullable()->unique();
            $table->text('summary_en')->nullable();
            $table->text('summary_ar')->nullable();
            $table->longText('challenge_en')->nullable();
            $table->longText('challenge_ar')->nullable();
            $table->longText('approach_en')->nullable();
            $table->longText('approach_ar')->nullable();
            $table->longText('outcome_en')->nullable();
            $table->longText('outcome_ar')->nullable();
            $table->longText('deliverables_en')->nullable();
            $table->longText('deliverables_ar')->nullable();
            $table->string('client_name')->nullable();
            $table->string('project_url', 2048)->nullable();
            $table->string('project_status', 20)->default('concept')->index();
            $table->boolean('display_permission_confirmed')->default(false)->index();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'project_status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->uuid('actor_user_id')->nullable();

            $table->string('action', 100);
            $table->string('entity_type', 100);
            $table->uuid('entity_id');

            $table->string('ip', 64)->nullable();
            $table->string('device_id', 255)->nullable();
            $table->text('user_agent')->nullable();

            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->json('meta')->nullable();

            $table->timestampTz('created_at')->nullable();

            $table->index(['org_id', 'entity_type', 'entity_id']);
            $table->index(['org_id', 'created_at']);

            $table->foreign('org_id')->references('id')->on('orgs');
            $table->foreign('actor_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

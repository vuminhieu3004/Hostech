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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->uuid('user_id');

            $table->string('device_id', 255)->nullable();
            $table->string('device_name', 255)->nullable();
            $table->string('device_platform', 50)->nullable();
            $table->string('device_fingerprint', 255)->nullable();
            $table->boolean('is_trusted')->default(false);

            $table->text('refresh_token_hash')->nullable();
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestampTz('last_seen_at')->nullable();

            $table->timestampTz('created_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampTz('revoked_at')->nullable();

            $table->index(['org_id', 'user_id', 'expires_at']);
            $table->index(['org_id', 'device_id']);

            $table->foreign('org_id')->references('id')->on('orgs');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');

            $table->enum('role', ['OWNER', 'MANAGER', 'STAFF', 'TENANT']);
            $table->string('full_name', 255);
            $table->string('phone', 30)->nullable();
            $table->string('email', 255)->nullable();

            $table->text('password_hash')->nullable();
            $table->timestampTz('phone_verified_at')->nullable();
            $table->timestampTz('email_verified_at')->nullable();

            $table->integer('failed_login_count')->default(0);
            $table->timestampTz('locked_until')->nullable();
            $table->timestampTz('last_login_at')->nullable();

            $table->string('invite_code', 64)->nullable();
            $table->timestampTz('invite_expires_at')->nullable();
            $table->uuid('invited_by_user_id')->nullable();

            $table->boolean('mfa_enabled')->default(false);
            $table->string('mfa_method', 20)->nullable();
            $table->text('mfa_secret_encrypted')->nullable();
            $table->timestampTz('mfa_enrolled_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestampTz('deleted_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestampsTz();

            $table->unique(['org_id', 'phone']);
            $table->unique(['org_id', 'email']);
            $table->index(['org_id', 'role']);
            $table->index(['org_id', 'invite_code']);

            $table->foreign('org_id')->references('id')->on('orgs');
            $table->foreign('invited_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

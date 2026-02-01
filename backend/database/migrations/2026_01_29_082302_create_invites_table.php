<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->uuid('created_by_user_id');

            // Target info
            $table->enum('target_role', ['MANAGER', 'STAFF', 'TENANT'])->comment('Role người được mời');
            $table->string('target_email', 255)->nullable();
            $table->string('target_phone', 30)->nullable();

            // Invite code
            $table->string('code', 64)->unique()->comment('Mã mời (UUID hoặc random)');
            $table->timestampTz('expires_at')->comment('Thời hạn mã mời');

            // Usage tracking
            $table->integer('max_uses')->default(1)->comment('Số lần sử dụng tối đa');
            $table->integer('used_count')->default(0)->comment('Số lần đã sử dụng');
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REVOKED', 'EXPIRED'])->default('PENDING');

            // Optional: assign to property/room
            $table->uuid('property_id')->nullable()->comment('Property mà user sẽ được assign (optional)');
            $table->uuid('room_id')->nullable()->comment('Room mà tenant sẽ được assign (optional)');

            // Accept tracking
            $table->uuid('accepted_by_user_id')->nullable()->comment('User ID sau khi accept');
            $table->timestampTz('accepted_at')->nullable();
            $table->timestampTz('revoked_at')->nullable();

            // Metadata
            $table->json('meta')->nullable()->comment('Thông tin bổ sung: message, permissions, etc.');

            $table->timestampsTz();

            // Indexes
            $table->index(['org_id', 'created_by_user_id']);
            $table->index(['org_id', 'status']);
            $table->index(['code', 'status']);
            $table->index(['target_email', 'org_id']);
            $table->index(['target_phone', 'org_id']);
            $table->index('expires_at');

            // Foreign keys
            $table->foreign('org_id')->references('id')->on('orgs')->onDelete('cascade');
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            $table->foreign('accepted_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};

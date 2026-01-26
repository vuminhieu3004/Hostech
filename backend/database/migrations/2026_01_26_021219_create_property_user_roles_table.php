<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('org_id');
            $table->uuid('property_id');
            $table->uuid('user_id');

            // role theo property (bám SQL)
            $table->enum('role', ['OWNER', 'MANAGER', 'STAFF', 'TENANT']);
            $table->boolean('is_active')->default(true);

            // optional: grant thêm permission riêng cho property (mở rộng sau)
            $table->json('permissions')->nullable();

            $table->timestampTz('created_at')->nullable();

            $table->unique(['org_id', 'property_id', 'user_id']);
            $table->index(['org_id', 'user_id']);
            $table->index(['org_id', 'property_id']);

            $table->foreign('org_id')->references('id')->on('orgs');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_user_roles');
    }
};

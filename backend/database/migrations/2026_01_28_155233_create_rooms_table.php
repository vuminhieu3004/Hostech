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
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->uuid('property_id');
            $table->string('code', 50)->comment('Mã phòng, VD: A101, B202');
            $table->string('name', 255)->comment('Tên phòng');
            $table->enum('type', ['studio', 'apartment', 'house', 'dormitory', 'other'])->default('apartment');
            $table->decimal('area', 8, 2)->nullable()->comment('Diện tích m2');
            $table->integer('floor')->nullable()->comment('Tầng');
            $table->integer('capacity')->default(1)->comment('Sức chứa (số người)');
            $table->decimal('base_price', 15, 2)->comment('Giá thuê cơ bản/tháng');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved'])->default('available');
            $table->text('description')->nullable();
            $table->json('amenities')->nullable()->comment('Tiện nghi: điều hòa, tủ lạnh, máy giặt...');
            $table->json('utilities')->nullable()->comment('Dịch vụ: điện, nước, internet...');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('org_id');
            $table->index('property_id');
            $table->unique(['property_id', 'code']); // Mã phòng unique trong property
            $table->index('status');

            // Foreign keys
            $table->foreign('org_id')->references('id')->on('orgs')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

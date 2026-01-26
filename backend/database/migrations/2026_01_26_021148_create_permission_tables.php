<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $morphKey = $columnNames['model_morph_key'] ?? 'model_id';

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestampsTz();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestampsTz();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $morphKey) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->uuid($morphKey); // UUID user.id

            $table->index([$morphKey, 'model_type'], 'mhp_model_id_model_type_idx');

            $table->foreign('permission_id')->references('id')->on($tableNames['permissions'])->onDelete('cascade');
            $table->primary(['permission_id', $morphKey, 'model_type'], 'mhp_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $morphKey) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->uuid($morphKey); // UUID user.id

            $table->index([$morphKey, 'model_type'], 'mhr_model_id_model_type_idx');

            $table->foreign('role_id')->references('id')->on($tableNames['roles'])->onDelete('cascade');
            $table->primary(['role_id', $morphKey, 'model_type'], 'mhr_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')->references('id')->on($tableNames['permissions'])->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on($tableNames['roles'])->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'rhp_primary');
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};

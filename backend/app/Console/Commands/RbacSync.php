<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RbacSync extends Command
{
    protected $signature = 'rbac:sync {--prune : Xoa permission khong con trong config}';
    protected $description = 'Sync roles/permissions from config/rbac.php into database';

    public function handle(): int
    {
        $guard = config('rbac.guard', 'sanctum');
        $allPerms = collect(config('rbac.permissions', []))->unique()->values();
        $roleDefs = config('rbac.roles', []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) Upsert permissions
        foreach ($allPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => $guard]);
        }

        // 2) Upsert roles + sync permissions
        foreach ($roleDefs as $roleName => $defs) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

            $expanded = $this->expandPermissions($defs, $allPerms->all());
            $role->syncPermissions($expanded);
        }

        // 3) Optional prune
        if ($this->option('prune')) {
            Permission::query()
                ->where('guard_name', $guard)
                ->whereNotIn('name', $allPerms->all())
                ->delete();
        }

        $this->info('RBAC synced.');
        return self::SUCCESS;
    }

    private function expandPermissions(array $defs, array $allPerms): array
    {
        if (in_array('*', $defs, true)) return $allPerms;

        $out = [];
        foreach ($defs as $d) {
            if (str_ends_with($d, '.*')) {
                $prefix = substr($d, 0, -2) . '.';
                foreach ($allPerms as $p) {
                    if (str_starts_with($p, $prefix)) $out[] = $p;
                }
            } else {
                $out[] = $d;
            }
        }
        return array_values(array_unique($out));
    }
}

<?php

$guard = 'sanctum';

$CRUD = ['view', 'create', 'update', 'delete'];
$modules = [
    // org-level
    'audit_logs' => ['view'],
    'users'      => $CRUD,

    // property-level
    'properties' => $CRUD,
    'rooms'      => $CRUD,
];

$custom = [
    'contracts.terminate',
];

$permissions = [];
foreach ($modules as $module => $actions) {
    foreach ($actions as $a) $permissions[] = "{$module}.{$a}";
}
$permissions = array_values(array_unique(array_merge($permissions, $custom)));
sort($permissions);

$scopes = [
    'audit_logs.*' => 'org',
    'users.*'      => 'org',

    'properties.*' => 'property',
    'rooms.*'      => 'property',
    'contracts.*'  => 'property',
];

return [
    'guard' => $guard,

    // map enum DB -> spatie role name (giữ đúng 4 actor)
    'role_aliases' => [
        'OWNER'   => 'Owner',
        'MANAGER' => 'Manager',
        'STAFF'   => 'Staff',
        'TENANT'  => 'Tenant',
    ],

    'permissions' => $permissions,
    'scopes'      => $scopes,

    // role matrix (hỗ trợ '*' và 'module.*')
    'roles' => [
        'Owner' => ['*'],
        'Manager' => [
            'audit_logs.view',
            'properties.*',
            'rooms.*',
            'contracts.terminate',
        ],
        'Staff' => [
            'properties.view',
            'rooms.view',
            'rooms.update',
        ],
        'Tenant' => [
            // tenant thường chỉ view dữ liệu của mình (policy ownership làm phần này)
        ],
    ],
];

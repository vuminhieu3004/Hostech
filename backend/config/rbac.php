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
    // nghiệp vụ
    'contracts.terminate',

    // CRUD phân chia nhà/khu cho staff/manager (org-level)
    'properties.staff.view',
    'properties.staff.assign',
    'properties.staff.revoke',
];

$permissions = [];
foreach ($modules as $module => $actions) {
    foreach ($actions as $a) $permissions[] = "{$module}.{$a}";
}
$permissions = array_values(array_unique(array_merge($permissions, $custom)));
sort($permissions);

$scopes = [
    // org-level
    'audit_logs.*' => 'org',
    'users.*'      => 'org',

    // org-level cho quản trị gán staff vào property
    'properties.staff.view'   => 'org',
    'properties.staff.assign' => 'org',
    'properties.staff.revoke' => 'org',

    // property-level dữ liệu nghiệp vụ
    'properties.*' => 'property',
    'rooms.*'      => 'property',
    'contracts.*'  => 'property',
];

return [
    'guard' => $guard,

    'role_aliases' => [
        'OWNER'   => 'Owner',
        'MANAGER' => 'Manager',
        'STAFF'   => 'Staff',
        'TENANT'  => 'Tenant',
    ],

    'permissions' => $permissions,
    'scopes'      => $scopes,

    'roles' => [
        'Owner' => ['*'],

        'Manager' => [
            'audit_logs.view',
            'properties.*',
            'rooms.*',
            'contracts.terminate',

            // cho phép Manager quản lý việc gán nhà/khu
            'properties.staff.view',
            'properties.staff.assign',
            'properties.staff.revoke',
        ],

        'Staff' => [
            'properties.view',
            'rooms.view',
            'rooms.update',
        ],

        'Tenant' => [
            // các tính năng cho phép tenant dùng được viết ở đây
        ],
    ],
];

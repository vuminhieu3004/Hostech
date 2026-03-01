<?php

$guard = 'sanctum';

$CRUD = ['view', 'create', 'update', 'delete'];

$modules = [
    // org-level
    'audit_logs' => ['view'],
    'users'      => $CRUD,

    // property-level - Quản lý nhà/khu/phòng
    'properties' => $CRUD,
    'rooms'      => $CRUD,
    'room_prices' => $CRUD, // Quản lý giá phòng & price history

];

$custom = [
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

    // properties.create là org-level (tạo mới property)
    'properties.create' => 'org',
    'properties.view'   => 'org', // Danh sách properties thuộc org

    // property-level cho update/delete cụ thể
    'properties.update' => 'property',
    'properties.delete' => 'property',

    // property-level dữ liệu phòng
    'rooms.*'      => 'property',

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

        //Các tính năng cho phép manager đc làm
        'Manager' => [
            'audit_logs.view',
            'properties.*',
            'rooms.*',
            'contracts.terminate',

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

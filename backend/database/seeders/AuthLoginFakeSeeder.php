<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthLoginFakeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ====== 1) Tạo ORG test ======
            // dùng org email để tránh tạo trùng khi seed nhiều lần
            $org = DB::table('orgs')->where('email', 'org@hostech.test')->first();

            if (!$org) {
                $orgId = (string) Str::uuid();
                DB::table('orgs')->insert([
                    'id' => $orgId,
                    'name' => 'Hostech Demo Org',
                    'phone' => '0909999999',
                    'email' => 'org@hostech.test',
                    'address' => 'Bangkok (demo)',
                    'timezone' => 'Asia/Bangkok',
                    'currency' => 'VND',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $org = DB::table('orgs')->where('id', $orgId)->first();
            }

            $orgId = $org->id;

            // helper upsert user theo (org_id + email/phone)
            $upsertUser = function (array $data) use ($orgId) {
                // ưu tiên match theo email nếu có, nếu không match theo phone
                $query = DB::table('users')->where('org_id', $orgId);
                if (!empty($data['email'])) {
                    $query->where('email', $data['email']);
                } else {
                    $query->where('phone', $data['phone']);
                }

                $exists = $query->first();

                if ($exists) {
                    DB::table('users')->where('id', $exists->id)->update(array_merge($data, [
                        'updated_at' => now(),
                    ]));
                    return $exists->id;
                }

                $id = (string) Str::uuid();
                DB::table('users')->insert(array_merge($data, [
                    'id' => $id,
                    'org_id' => $orgId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                return $id;
            };

            // ====== 2) OWNER (đăng nhập password) ======
            $ownerId = $upsertUser([
                'role' => 'OWNER',
                'full_name' => 'Owner Demo',
                'phone' => '0368326144',
                'email' => 'Vutienhieu202@gmail.com',
                'password_hash' => Hash::make('12345678'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'failed_login_count' => 0,
                'locked_until' => null,
                'last_login_at' => null,
                'invite_code' => null,
                'invite_expires_at' => null,
                'invited_by_user_id' => null,
                'mfa_enabled' => 0,
                'mfa_method' => null,
                'mfa_secret_encrypted' => null,
                'mfa_enrolled_at' => null,
                'is_active' => 1,
                'deleted_at' => null,
                'meta' => json_encode(['seed' => true]),
            ]);

            // ====== 3) STAFF (đăng nhập password) ======
            $staffId = $upsertUser([
                'role' => 'STAFF',
                'full_name' => 'Staff Demo',
                'phone' => '0902222222',
                'email' => 'staff@hostech.test',
                'password_hash' => Hash::make('12345678'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'failed_login_count' => 0,
                'locked_until' => null,
                'last_login_at' => null,
                'invite_code' => null,
                'invite_expires_at' => null,
                'invited_by_user_id' => $ownerId,
                'mfa_enabled' => 0,
                'mfa_method' => null,
                'mfa_secret_encrypted' => null,
                'mfa_enrolled_at' => null,
                'is_active' => 1,
                'deleted_at' => null,
                'meta' => json_encode(['seed' => true]),
            ]);

            // ====== 4) TENANT (đăng nhập OTP, không có password) ======
            $tenantId = $upsertUser([
                'role' => 'TENANT',
                'full_name' => 'Tenant Demo',
                'phone' => '0903333333',
                'email' => null,
                'password_hash' => null,
                'phone_verified_at' => now(),
                'email_verified_at' => null,
                'failed_login_count' => 0,
                'locked_until' => null,
                'last_login_at' => null,
                'invite_code' => null,
                'invite_expires_at' => null,
                'invited_by_user_id' => $ownerId,
                'mfa_enabled' => 0,
                'mfa_method' => null,
                'mfa_secret_encrypted' => null,
                'mfa_enrolled_at' => null,
                'is_active' => 1,
                'deleted_at' => null,
                'meta' => json_encode(['seed' => true]),
            ]);

            // ====== 5) 1 user bị block (test is_active=false) ======
            $blockedId = $upsertUser([
                'role' => 'STAFF',
                'full_name' => 'Staff Blocked',
                'phone' => '0904444444',
                'email' => 'blocked@hostech.test',
                'password_hash' => Hash::make('12345678'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'failed_login_count' => 0,
                'locked_until' => null,
                'last_login_at' => null,
                'invite_code' => null,
                'invite_expires_at' => null,
                'invited_by_user_id' => $ownerId,
                'mfa_enabled' => 0,
                'mfa_method' => null,
                'mfa_secret_encrypted' => null,
                'mfa_enrolled_at' => null,
                'is_active' => 0, // blocked
                'deleted_at' => null,
                'meta' => json_encode(['seed' => true, 'blocked' => true]),
            ]);

            // ====== 6) 1 user bị lock tạm (test locked_until) ======
            $lockedId = $upsertUser([
                'role' => 'STAFF',
                'full_name' => 'Staff Locked',
                'phone' => '0905555555',
                'email' => 'locked@hostech.test',
                'password_hash' => Hash::make('12345678'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'failed_login_count' => 0,
                'locked_until' => now()->addMinutes(15), // locked 15 phút
                'last_login_at' => null,
                'invite_code' => null,
                'invite_expires_at' => null,
                'invited_by_user_id' => $ownerId,
                'mfa_enabled' => 0,
                'mfa_method' => null,
                'mfa_secret_encrypted' => null,
                'mfa_enrolled_at' => null,
                'is_active' => 1,
                'deleted_at' => null,
                'meta' => json_encode(['seed' => true, 'locked' => true]),
            ]);

            $this->command?->info(" Auth fake data seeded!");
            $this->command?->info("ORG_ID: {$orgId}");
            $this->command?->info("Owner login: owner@hostech.test / 12345678");
            $this->command?->info("Staff login: staff@hostech.test / 12345678");
            $this->command?->info("Tenant OTP phone: 0903333333");
            $this->command?->info("Blocked: blocked@hostech.test / 12345678 (is_active=0)");
            $this->command?->info("Locked: locked@hostech.test / 12345678 (locked_until=+15m)");
        });
    }
}
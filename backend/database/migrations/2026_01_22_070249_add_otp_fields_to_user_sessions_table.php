<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->string('status', 20)->default('PENDING_OTP');

            $table->text('otp_hash')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->unsignedInteger('otp_attempts')->default(0);
            $table->timestamp('otp_sent_at')->nullable();

            $table->index(['org_id', 'user_id', 'status']);
            $table->index(['org_id', 'otp_expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropIndex(['org_id', 'user_id', 'status']);
            $table->dropIndex(['org_id', 'otp_expires_at']);

            $table->dropColumn([
                'status',
                'otp_hash',
                'otp_expires_at',
                'otp_verified_at',
                'otp_attempts',
                'otp_sent_at',
            ]);
        });
    }
};

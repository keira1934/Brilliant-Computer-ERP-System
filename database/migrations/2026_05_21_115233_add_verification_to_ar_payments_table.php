<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ar_payments', function (Blueprint $table) {
            // Verification status for the payment record
            $table->enum('status', ['Pending Verification', 'Verified', 'Rejected'])
                  ->default('Pending Verification')
                  ->after('notes');

            // Who verified/rejected and when
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('rejection_reason')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('ar_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['status', 'verified_at', 'rejection_reason']);
        });
    }
};

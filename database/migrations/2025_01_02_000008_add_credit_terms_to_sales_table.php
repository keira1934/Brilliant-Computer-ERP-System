<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('is_credit_sale')->default(false)->after('payment_method');
            $table->unsignedSmallInteger('payment_terms_days')->default(0)->after('is_credit_sale');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['is_credit_sale', 'payment_terms_days']);
        });
    }
};

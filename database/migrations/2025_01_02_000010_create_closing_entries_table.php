<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('closing_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_period_id')->constrained('financial_periods')->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->date('closing_date');
            $table->decimal('revenue_closed', 15, 2)->default(0);
            $table->decimal('expenses_closed', 15, 2)->default(0);
            $table->decimal('net_income', 15, 2)->default(0);
            $table->timestamps();

            $table->unique('financial_period_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closing_entries');
    }
};

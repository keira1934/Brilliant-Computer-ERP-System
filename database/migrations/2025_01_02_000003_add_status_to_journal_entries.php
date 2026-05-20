<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('journal_number', 30)->nullable()->unique()->after('id');
            $table->enum('status', ['draft', 'posted', 'reversed', 'cancelled'])
                  ->default('posted')  // default posted for backward compat with existing data
                  ->after('reference_id');
            $table->timestamp('posted_at')->nullable()->after('status');
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null')->after('posted_at');
            $table->foreignId('reversed_by_entry_id')->nullable()->after('posted_by');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('reversed_by_entry_id');
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['posted_by']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['journal_number', 'status', 'posted_at', 'posted_by', 'reversed_by_entry_id', 'created_by']);
        });
    }
};

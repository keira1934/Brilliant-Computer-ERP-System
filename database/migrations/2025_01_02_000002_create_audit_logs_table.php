<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('module', 50);           // e.g. 'journal', 'sale', 'purchase', 'payroll'
            $table->string('action', 50);            // e.g. 'create', 'update', 'delete', 'post', 'reverse'
            $table->string('auditable_type')->nullable(); // e.g. 'App\Models\JournalEntry'
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Audit logs are append-only; no updated_at needed
            $table->index(['module', 'action']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

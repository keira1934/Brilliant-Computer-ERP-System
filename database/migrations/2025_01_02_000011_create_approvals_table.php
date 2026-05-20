<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approval_number', 30)->unique();
            $table->string('module', 50);
            $table->string('approvable_type');
            $table->unsignedBigInteger('approvable_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['approvable_type', 'approvable_id']);
            $table->index(['module', 'status']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE purchases MODIFY status ENUM('Draft','Pending Approval','Approved','Ordered','Received','Paid','Cancelled') NOT NULL DEFAULT 'Draft'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE purchases MODIFY status ENUM('Draft','Ordered','Received','Paid','Cancelled') NOT NULL DEFAULT 'Draft'");
        }

        Schema::dropIfExists('approvals');
    }
};

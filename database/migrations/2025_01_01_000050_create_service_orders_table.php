<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->enum('device_type', ['Laptop', 'Printer', 'CPU', 'All-in-One', 'Other']);
            $table->string('brand')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('problem_description');
            $table->text('diagnosis')->nullable();
            $table->decimal('service_cost', 15, 2)->nullable();
            $table->enum('status', ['Received', 'InProgress', 'Done', 'Completed'])->default('Received');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};

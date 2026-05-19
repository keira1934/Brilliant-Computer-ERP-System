<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ar_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['Open', 'Partially Paid', 'Paid', 'Cancelled'])->default('Open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('invoice_date');
            $table->index('due_date');
        });

        Schema::create('ar_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 30)->unique();
            $table->foreignId('ar_invoice_id')->constrained('ar_invoices')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['Cash', 'Transfer', 'Other'])->default('Cash');
            $table->string('reference', 80)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'payment_date']);
        });

        Schema::create('ap_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['Open', 'Partially Paid', 'Paid', 'Cancelled'])->default('Open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'status']);
            $table->index('invoice_date');
            $table->index('due_date');
        });

        Schema::create('ap_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 30)->unique();
            $table->foreignId('ap_invoice_id')->constrained('ap_invoices')->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['Cash', 'Transfer', 'Other'])->default('Cash');
            $table->string('reference', 80)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'payment_date']);
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number', 30)->unique();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->date('movement_date');
            $table->enum('movement_type', ['purchase_receipt', 'sale_issue', 'adjustment', 'opening']);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->integer('balance_qty')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('adjusting_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->date('adjustment_date');
            $table->enum('adjustment_type', ['depreciation', 'accrual', 'prepaid', 'inventory', 'other'])->default('other');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['Posted', 'Reversed'])->default('Posted');
            $table->timestamps();

            $table->index(['adjustment_date', 'adjustment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjusting_entries');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('ap_payments');
        Schema::dropIfExists('ap_invoices');
        Schema::dropIfExists('ar_payments');
        Schema::dropIfExists('ar_invoices');
    }
};

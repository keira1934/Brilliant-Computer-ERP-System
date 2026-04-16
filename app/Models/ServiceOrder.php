<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrder extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'device_type', 'brand',
        'serial_number', 'problem_description', 'diagnosis',
        'service_cost', 'status', 'received_at', 'completed_at', 'notes',
    ];

    protected $casts = [
        'received_at'  => 'datetime',
        'completed_at' => 'datetime',
        'service_cost' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'Received'   => 'badge status-received',
            'InProgress' => 'badge status-inprogress',
            'Done'       => 'badge status-done',
            'Completed'  => 'badge status-completed',
            default      => 'badge badge-gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'InProgress' => 'Dalam Proses',
            'Received'   => 'Diterima',
            'Done'       => 'Selesai',
            'Completed'  => 'Lunas',
            default      => $this->status,
        };
    }
}

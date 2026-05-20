<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false; // We only use created_at, set via DB default

    protected $fillable = [
        'user_id', 'module', 'action', 'auditable_type', 'auditable_id',
        'old_values', 'new_values', 'ip_address', 'description', 'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * AuditLog is IMMUTABLE. Disable update/delete.
     */
    public function save(array $options = [])
    {
        if ($this->exists) {
            throw new \RuntimeException('Audit logs are immutable and cannot be modified.');
        }
        return parent::save($options);
    }

    public function delete()
    {
        throw new \RuntimeException('Audit logs are immutable and cannot be deleted.');
    }
}

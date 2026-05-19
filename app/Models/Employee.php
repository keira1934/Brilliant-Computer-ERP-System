<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_code', 'name', 'position', 'phone', 'email',
        'address', 'salary_type', 'base_salary', 'join_date', 'is_active',
    ];

    protected $casts = [
        'join_date'   => 'date',
        'is_active'   => 'boolean',
        'base_salary' => 'decimal:2',
    ];

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}

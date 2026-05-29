<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Rental extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'tool_id',
        'user_id',
        'checkout_at',
        'due_at',
        'returned_at',
        'daily_rate',
        'deposit',
        'discount',
        'late_fee',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'checkout_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOverdue(): bool
    {
        return !in_array($this->status, ['Pending', 'Returned'])
            && $this->due_at
            && $this->due_at->isPast();
    }
}

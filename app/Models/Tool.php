<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Tool extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'brand',
        'model_number',
        'serial_number',
        'description',
        'condition_notes',
        'condition_updated_at',
        'condition_updated_by',
        'daily_rate',
        'status',
        'image',
    ];

    protected $casts = [
        'condition_updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function conditionUpdatedBy()
    {
        return $this->belongsTo(User::class, 'condition_updated_by');
    }
}

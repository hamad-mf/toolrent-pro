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
        'daily_rate',
        'status',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}

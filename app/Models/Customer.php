<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Customer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'id_type',
        'id_number',
        'address',
        'notes',
        'is_active',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}

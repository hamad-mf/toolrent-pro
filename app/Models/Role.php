<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    public const SUPER_ADMIN = 'super-admin';
    public const SHOP_ADMIN = 'shop-admin';
    public const MANAGER = 'manager';
    public const COUNTER_STAFF = 'counter-staff';
    public const FLOOR_STAFF = 'floor-staff';
}

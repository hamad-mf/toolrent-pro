<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Category extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'slug'];

    public function tools()
    {
        return $this->hasMany(Tool::class);
    }
}

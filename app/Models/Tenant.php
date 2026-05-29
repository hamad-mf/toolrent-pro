<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'system_name',
        'custom_css',
        'plan',
        'max_users',
        'max_tools',
        'is_active',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasFeature(string $feature): bool
    {
        // Default features enabled if null
        if (!$this->features) return true;
        return in_array($feature, $this->features);
    }
}

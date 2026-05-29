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

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function hasFeature(string $feature): bool
    {
        // If features is null, we assume all core features are enabled by default
        if (is_null($this->features)) {
            $coreFeatures = ['categories', 'tools', 'customers', 'rentals', 'reports', 'invoicing', 'qrcode'];
            return in_array($feature, $coreFeatures);
        }

        return in_array($feature, (array)$this->features);
    }
}

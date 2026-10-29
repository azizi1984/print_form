<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends SpatiePermission
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'menu_id',
        'parent_id',
        'order',
    ];

    /**
     * Parent permission in the tree hierarchy.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    /**
     * Child permissions in the tree hierarchy, sorted by display order.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Permission::class, 'parent_id')->orderBy('order', 'ASC');
    }

    /**
     * Get user-friendly formatted title from PascalCase name.
     */
    public function getFriendlyTitleAttribute(): string
    {
        $spaced = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $this->name);
        $spaced = preg_replace('/(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $spaced);
        $spaced = str_replace(['_', '-', '.'], ' ', $spaced);
        return trim(preg_replace('/\s+/', ' ', $spaced));
    }
}

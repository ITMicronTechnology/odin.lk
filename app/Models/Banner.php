<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle', 'image', 'link', 'position', 'is_active', 'sort_order'])]
class Banner extends Model
{
    /**
     * Where a banner can be placed on the home page.
     *
     * @var array<string, string>
     */
    public const POSITIONS = [
        'slider' => 'Main slider (1600 x 520)',
        'side' => 'Promo banner (800 x 300)',
        'wide' => 'Wide promo strip (1600 x 300)',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Banner>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): string => asset('storage/'.$this->image));
    }
}

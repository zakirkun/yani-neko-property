<?php

namespace App\Models;

use App\Enums\PropertyPurpose;
use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id', 'property_type_id', 'title', 'slug', 'description', 'purpose', 'status',
        'price', 'address', 'city', 'state', 'postcode', 'latitude', 'longitude', 'bedrooms',
        'bathrooms', 'area', 'is_featured', 'published_at',
    ];

    protected $casts = [
        'purpose' => PropertyPurpose::class,
        'status' => PropertyStatus::class,
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'area' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Property $property) {
            if (! $property->slug || $property->isDirty('title')) {
                $base = Str::slug($property->title) ?: 'property';
                $slug = $base;
                $suffix = 2;

                while (static::query()->where('slug', $slug)->whereKeyNot($property->getKey())->exists()) {
                    $slug = $base.'-'.$suffix++;
                }

                $property->slug = $slug;
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PropertyStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}

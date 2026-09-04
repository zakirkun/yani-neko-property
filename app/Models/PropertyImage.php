<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'path', 'alt_text', 'sort_order', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean', 'sort_order' => 'integer'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

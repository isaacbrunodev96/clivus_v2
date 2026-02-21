<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\EntityScope;

class IndirectCost extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new EntityScope);
    }
    protected $fillable = [
        'user_id',
        'description',
        'category',
        'type',
        'monthly_value',
        'cost_center',
        'include_in_allocation',
        'reference_month',
        'reference_year',
    ];

    protected $casts = [
        'monthly_value' => 'decimal:2',
        'include_in_allocation' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

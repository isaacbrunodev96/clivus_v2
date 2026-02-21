<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\EntityScope;

class Account extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new EntityScope);
    }
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'bank',
        'agency',
        'account_number',
        'holder',
        'cpf',
        'company_id',
        'pix_key',
        'balance',
        'active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\EntityScope;

class Transaction extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new EntityScope);
    }
    protected $fillable = [
        'account_id',
        'user_id',
        'description',
        'type',
        'payment_method',
        'amount',
        'date',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

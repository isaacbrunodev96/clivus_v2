<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\EntityScope;

class Receivable extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new EntityScope);
    }
    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'due_date',
        'type',
        'account_id',
        'category_id',
        'contact_id',
        'status',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'received_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}

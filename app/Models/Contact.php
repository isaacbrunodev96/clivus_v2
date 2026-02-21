<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\EntityScope;

class Contact extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new EntityScope);
    }
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'cpf_cnpj',
        'email',
        'phone',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class);
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

protected $fillable = [
    'number',
    'prefix',
    'name',
    'objectives',
];

    protected $casts = [
        'objectives' => 'array',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleItem extends Model
{
    protected $fillable = [
        'module_id',
        'type',
        'title',
        'position',
        'data',
        'rubric_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('position');
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
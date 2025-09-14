<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    protected $fillable = [
        'module_item_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'position',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function moduleItem(): BelongsTo
    {
        return $this->belongsTo(ModuleItem::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
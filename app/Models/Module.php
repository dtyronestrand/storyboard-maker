<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
protected $fillable = [
    'title',
    'number',
    'objectives',
    'course_id',
    'items',
];

    protected $casts = [
        'objectives' => 'array',
        'items' => AsArrayObject::class,
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}

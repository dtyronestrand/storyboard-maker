<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
 protected $fillable = [
    'title',
    'performance_levels',
    'criteria',
 ];

    protected $casts = [
        'performance_levels' => 'array',
        'criteria' => 'array',
    ];

    public function moduleitems(): HasMany
    {
        return $this->hasMany(ModuleItem::class);
    }
}

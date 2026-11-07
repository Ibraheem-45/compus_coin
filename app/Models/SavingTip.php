<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingTip extends Model
{
    protected $primaryKey = 'tip_id';

    protected $fillable = [
        'user_id',
        'category',
        'recommendation_key',
        'title',
        'content',
        'impact_score',
        'is_pinned',
        'is_dismissed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
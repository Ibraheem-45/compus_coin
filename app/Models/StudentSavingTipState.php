<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSavingTipState extends Model
{
    protected $fillable = [
        'user_id',
        'tip_id',
        'is_dismissed',
    ];

    public function tip(): BelongsTo
    {
        return $this->belongsTo(SavingTip::class, 'tip_id', 'tip_id');
    }
}
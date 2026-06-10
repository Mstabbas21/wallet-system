<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchases extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'amount_paid', 
        'currency', 'status', 'purchased_at', 'expires_at'
    ];

    // العملية تابعة ليوزر معين
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // العملية تابعة لخدمة معينة
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}

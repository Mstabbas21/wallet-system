<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'currency',
        'description',
    ];

    /**
     * Relationship inverse: Kel transaction btotba3 la User wahad
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
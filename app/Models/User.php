<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    /**
     * El fillable lezem ykoun fi el amount kermel تقدر ta3mel create/update lal balance
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'currency',
    ];

    /**
     * El attributes el makhfiyye lama traje3 el User ka JSON (API)
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Casting kermel el password ydal hashed deyman
     * w el amount ytratar ka decimal/float kermel el calculations
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Relationship: User has many Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transaction::class);
    }


public function phurchases(): HasMany
{
    return $this->hasMany(Purchases::class);
}

}

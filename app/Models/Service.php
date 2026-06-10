<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'category_id', 'parent_id', 'name', 'description', 
        'price', 'currency', 'points_value', 'duration_days', 'is_active'
    ];

    // تبعية الخدمة لقسم معين
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // علاقة الخدمة الأساسية بالباقات التابعة لها (Sub-services)
    public function subServices(): HasMany
    {
        return $this->hasMany(Service::class, 'parent_id');
    }

    // علاقة الباقة بالخدمة الأم
    public function parentService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    // مين اليوزرز اللي اشتروا هيدي الخدمة (عبر جدول المشتريات)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'purchases')
                    ->withPivot('amount_paid', 'currency', 'status', 'expires_at')
                    ->withTimestamps();
    }
public function children() {
    // El service fiyo ykon 3ando ktir wled
    return $this->hasMany(Service::class, 'parent_id');
}

public function parent() {
    // El service el wald 3ando emm wahde
    return $this->belongsTo(Service::class, 'parent_id');
}


}


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        // الربط مع اليوزر والخدمة
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('service_id')->constrained()->onDelete('cascade');

        // منسجل السعر والعملة وقت الشراء (عشان لو تغير السعر بالمستقبل بجدول الـ services)
        $table->decimal('amount_paid', 16, 2);
        $table->string('currency');

        // حالة العملية (مثلاً: ناجحة، قيد الانتظار، أو ملغاة)
        $table->string('status')->default('completed'); 
        
        $table->timestamp('purchased_at')->useCurrent();
        $table->timestamp('expires_at')->nullable(); // منحسبه من الـ duration_days تبع الخدمة
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('purchases');
    }
};

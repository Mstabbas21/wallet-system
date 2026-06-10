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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // ربط العملية بالمستخدم (Foreign Key)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // المبلغ (Decimal بكون أدق للعملات من الـ Float)
            $table->decimal('amount', 15, 2);
            
            // نوع العملية (إيداع أو سحب)
            $table->enum('type', ['deposit', 'withdraw']);
            
            // ملاحظات اختيارية (مثلاً: "إيداع من الصراف الآلي")
            $table->string('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
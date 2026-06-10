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
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        // الربط مع القسم
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        
        // الربط مع الخدمة الأب (Self-referencing)
        $table->unsignedBigInteger('parent_id')->nullable();
        $table->foreign('parent_id')->references('id')->on('services')->onDelete('cascade');

        $table->string('name'); // مثلاً: Netflix Premium
        $table->text('description')->nullable();
        $table->decimal('price', 16, 2); // السعر
        $table->string('currency')->default('USD'); // عملة السعر
        
        // حقول إضافية للـ Logic تبعك
        $table->integer('points_value')->nullable(); // إذا عم يشتري نقاط
        $table->integer('duration_days')->nullable(); // إذا اشتراك مدته محدودة
        
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
       Schema::dropIfExists('services');
    }
};

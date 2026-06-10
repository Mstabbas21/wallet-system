<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            // l-shakhs el ba3at el masari
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');

            // l-shakhs el stalam el masari
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');

            $table->decimal('amount', 15, 2);
            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
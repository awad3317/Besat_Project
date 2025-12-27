<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_code_user', function (Blueprint $table) {
            // $table->primary(['user_id', 'discount_code_id']);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discount_code_id')->constrained()->cascadeOnDelete();
            $table->integer('usage_count')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['user_id', 'discount_code_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_code_user');
    }
};

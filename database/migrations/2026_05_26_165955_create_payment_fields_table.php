<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_step_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('label');
            $table->string('hint')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('text');
            $table->integer('min_length')->nullable();
            $table->integer('max_length')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->string('default_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_fields');
    }
};
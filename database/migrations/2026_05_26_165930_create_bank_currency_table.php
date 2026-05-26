<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_currency', function (Blueprint $table) {
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->primary(['bank_id', 'currency_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_currency');
    }
};
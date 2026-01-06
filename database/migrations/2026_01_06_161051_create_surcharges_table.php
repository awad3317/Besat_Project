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
        Schema::create('surcharges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['time_based', 'conditional']);
            $table->string('condition_key')->nullable();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->decimal('amount', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surcharges');
    }
};

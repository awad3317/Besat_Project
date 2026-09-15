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
        Schema::create('driver_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('trips_count')->default(0);
            $table->decimal('driver_owes_app', 10, 2)->default(0);
            $table->decimal('app_owes_driver', 10, 2)->default(0);
            $table->decimal('net_balance', 10, 2)->default(0);
            $table->enum('settlement_action', ['app_pays_driver', 'driver_pays_app'])->default('driver_pays_app');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_settlements');
    }
};


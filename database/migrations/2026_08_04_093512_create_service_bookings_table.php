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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('service_type');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');

            $table->string('client_name'); 
            $table->integer('passenger_count'); 
            $table->text('notes')->nullable(); 

            $table->string('start_address');
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 10, 8)->nullable();

            $table->string('end_address');
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 10, 8)->nullable();

            $table->integer('vehicles_count')->nullable()->default(1);
            $table->boolean('wants_ac')->default(false);

            $table->dateTime('trip_datetime')->nullable();
            $table->string('duration')->nullable();

            $table->json('service_details')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid'])->default('unpaid');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};

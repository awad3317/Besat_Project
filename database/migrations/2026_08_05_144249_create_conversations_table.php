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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['support', 'request'])->default('request');
            $table->foreignId('request_id')->nullable()->constrained('requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('cascade'); 
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->onDelete('set null'); 
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('user_unread_count')->default(0);
            $table->unsignedInteger('participant_unread_count')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'updated_at']);
            $table->index(['driver_id', 'updated_at']);
            $table->index(['request_id']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

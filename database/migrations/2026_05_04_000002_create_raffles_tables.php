<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('line_id')->nullable();
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->unsignedInteger('start_number')->default(1);
            $table->unsignedInteger('next_number')->default(1);
            $table->unsignedInteger('max_numbers')->nullable();
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('winner_number')->nullable();
            $table->timestamps();
        });

        Schema::create('raffle_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->timestamps();
            $table->unique(['raffle_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raffle_numbers');
        Schema::dropIfExists('raffles');
    }
};

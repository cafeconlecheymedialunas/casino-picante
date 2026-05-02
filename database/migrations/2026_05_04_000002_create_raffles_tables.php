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
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'active', 'ended'])->default('upcoming');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('number_type', ['4digits', 'infinite'])->default('infinite');
            $table->unsignedInteger('max_numbers')->nullable(); // for 4digits: 9999
            $table->unsignedInteger('next_number')->default(1); // auto-increment counter
            $table->timestamps();
        });

        Schema::create('raffle_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position'); // 1st, 2nd, 3rd place
            $table->string('prize_description');
            $table->decimal('prize_amount', 12, 2)->nullable();
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
        Schema::dropIfExists('raffle_positions');
        Schema::dropIfExists('raffles');
    }
};

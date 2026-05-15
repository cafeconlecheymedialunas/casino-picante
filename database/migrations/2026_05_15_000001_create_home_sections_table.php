<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('kicker')->nullable();
            $table->string('highlight')->nullable();
            $table->text('content')->nullable();
            $table->string('action_text')->nullable();
            $table->string('action_url')->nullable();
            $table->string('raffle_type')->nullable();
            $table->string('raffle_ids')->nullable();
            $table->string('post_type')->nullable();
            $table->string('post_ids')->nullable();
            $table->string('bonus_type')->nullable();
            $table->string('bonus_ids')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};

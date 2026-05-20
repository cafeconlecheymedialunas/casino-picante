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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade'); // El usuario con rol 'cajero'
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->json('contacts')->nullable(); // WhatsApp, Telegram, email, etc.
            $table->json('branding')->nullable(); // Colores, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('lines', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
        });
        Schema::dropIfExists('vendors');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        Schema::table('home_sections', function (Blueprint $table): void {
            if (Schema::hasIndex('home_sections', 'home_sections_section_key_unique')) {
                $table->dropUnique('home_sections_section_key_unique');
            }
        });

        Schema::table('home_sections', function (Blueprint $table): void {
            if (! Schema::hasIndex('home_sections', 'home_sections_vendor_id_section_key_unique')) {
                $table->unique(['vendor_id', 'section_key']);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        Schema::table('home_sections', function (Blueprint $table): void {
            if (Schema::hasIndex('home_sections', 'home_sections_vendor_id_section_key_unique')) {
                $table->dropUnique('home_sections_vendor_id_section_key_unique');
            }
        });

        Schema::table('home_sections', function (Blueprint $table): void {
            if (! Schema::hasIndex('home_sections', 'home_sections_section_key_unique')) {
                $table->unique('section_key');
            }
        });
    }
};

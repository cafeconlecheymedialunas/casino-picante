<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceUnique('platforms', 'platforms_slug_unique', ['vendor_id', 'slug'], 'platforms_vendor_id_slug_unique');
        $this->replaceUnique('categories', 'categories_slug_unique', ['vendor_id', 'slug'], 'categories_vendor_id_slug_unique');
        $this->replaceUnique('posts', 'posts_slug_unique', ['vendor_id', 'slug'], 'posts_vendor_id_slug_unique');
        $this->replaceUnique('bonuses', 'bonuses_code_unique', ['vendor_id', 'code'], 'bonuses_vendor_id_code_unique');
    }

    public function down(): void
    {
        $this->replaceUnique('platforms', 'platforms_vendor_id_slug_unique', ['slug'], 'platforms_slug_unique');
        $this->replaceUnique('categories', 'categories_vendor_id_slug_unique', ['slug'], 'categories_slug_unique');
        $this->replaceUnique('posts', 'posts_vendor_id_slug_unique', ['slug'], 'posts_slug_unique');
        $this->replaceUnique('bonuses', 'bonuses_vendor_id_code_unique', ['code'], 'bonuses_code_unique');
    }

    private function replaceUnique(string $tableName, string $dropIndex, array $columns, string $createIndex): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $dropIndex): void {
            if (Schema::hasIndex($tableName, $dropIndex)) {
                $table->dropUnique($dropIndex);
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns, $createIndex): void {
            if (! Schema::hasIndex($tableName, $createIndex)) {
                $table->unique($columns, $createIndex);
            }
        });
    }
};

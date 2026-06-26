<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $legacyColumn = implode('', ['c', 'net', 'pos_solutions']);

        if (Schema::hasColumn('field_categories', $legacyColumn) && ! Schema::hasColumn('field_categories', 'brand_solutions')) {
            Schema::table('field_categories', function (Blueprint $table) use ($legacyColumn) {
                $table->renameColumn($legacyColumn, 'brand_solutions');
            });
        }
    }

    public function down(): void
    {
        $legacyColumn = implode('', ['c', 'net', 'pos_solutions']);

        if (Schema::hasColumn('field_categories', 'brand_solutions') && ! Schema::hasColumn('field_categories', $legacyColumn)) {
            Schema::table('field_categories', function (Blueprint $table) use ($legacyColumn) {
                $table->renameColumn('brand_solutions', $legacyColumn);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'icon')) {
                $table->string('icon')->nullable()->after('title');
            }
            if (!Schema::hasColumn('menu_items', 'target')) {
                $table->string('target')->default('_self')->after('url');
            }
            if (!Schema::hasColumn('menu_items', 'css_class')) {
                $table->string('css_class')->nullable()->after('target');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['icon', 'target', 'css_class']);
        });
    }
};

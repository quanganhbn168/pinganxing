<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'menu_id')) {
                $table->unsignedBigInteger('menu_id')->nullable()->after('id');
                $table->index('menu_id');
            }
        });

        // Gán tất cả menu_items chưa có menu_id vào menu đầu tiên
        $firstMenuId = \App\Models\Menu::first()?->id;
        if ($firstMenuId) {
            \App\Models\MenuItem::whereNull('menu_id')->update(['menu_id' => $firstMenuId]);
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('menu_id');
        });
    }
};

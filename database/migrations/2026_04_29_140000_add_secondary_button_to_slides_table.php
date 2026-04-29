<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('slides', 'button_text_2')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->string('button_text_2')->nullable()->after('button_text');
            });
        }

        if (! Schema::hasColumn('slides', 'link_2')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->string('link_2')->nullable()->after('button_text_2');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('slides', 'link_2')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->dropColumn('link_2');
            });
        }

        if (Schema::hasColumn('slides', 'button_text_2')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->dropColumn('button_text_2');
            });
        }
    }
};

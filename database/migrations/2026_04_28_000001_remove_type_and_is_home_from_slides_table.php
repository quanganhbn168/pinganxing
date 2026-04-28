<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $slideIds = DB::table('slides')
            ->orderBy('position')
            ->orderBy('id')
            ->pluck('id');

        foreach ($slideIds as $index => $id) {
            DB::table('slides')
                ->where('id', $id)
                ->update(['position' => $index + 1]);
        }

        if (Schema::hasColumn('slides', 'type') || Schema::hasColumn('slides', 'is_home')) {
            Schema::table('slides', function (Blueprint $table) {
                if (Schema::hasColumn('slides', 'type')) {
                    $table->dropColumn('type');
                }

                if (Schema::hasColumn('slides', 'is_home')) {
                    $table->dropColumn('is_home');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            if (! Schema::hasColumn('slides', 'type')) {
                $table->string('type')->default('home')->after('image_id');
            }

            if (! Schema::hasColumn('slides', 'is_home')) {
                $table->boolean('is_home')->default(true)->after('status');
            }
        });
    }
};

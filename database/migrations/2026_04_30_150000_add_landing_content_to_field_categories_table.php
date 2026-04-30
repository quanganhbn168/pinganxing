<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('field_categories', 'banner_id')) {
                $table->unsignedBigInteger('banner_id')->nullable()->after('image_id');
            }

            if (! Schema::hasColumn('field_categories', 'solution_overview')) {
                $table->text('solution_overview')->nullable()->after('content');
            }

            if (! Schema::hasColumn('field_categories', 'business_challenges')) {
                $table->json('business_challenges')->nullable()->after('solution_overview');
            }

            if (! Schema::hasColumn('field_categories', 'cnetpos_solutions')) {
                $table->json('cnetpos_solutions')->nullable()->after('business_challenges');
            }

            if (! Schema::hasColumn('field_categories', 'key_features')) {
                $table->json('key_features')->nullable()->after('cnetpos_solutions');
            }

            if (! Schema::hasColumn('field_categories', 'impact_stats')) {
                $table->json('impact_stats')->nullable()->after('key_features');
            }

            if (! Schema::hasColumn('field_categories', 'implementation_steps')) {
                $table->json('implementation_steps')->nullable()->after('impact_stats');
            }

            if (! Schema::hasColumn('field_categories', 'related_project_ids')) {
                $table->json('related_project_ids')->nullable()->after('implementation_steps');
            }

        });
    }

    public function down(): void
    {
        Schema::table('field_categories', function (Blueprint $table) {
            foreach ([
                'related_project_ids',
                'implementation_steps',
                'impact_stats',
                'key_features',
                'cnetpos_solutions',
                'business_challenges',
                'solution_overview',
                'banner_id',
            ] as $column) {
                if (Schema::hasColumn('field_categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'project_overview')) {
                $table->longText('project_overview')->nullable()->after('description');
            }

            if (! Schema::hasColumn('projects', 'business_problems')) {
                $table->json('business_problems')->nullable()->after('project_overview');
            }

            if (! Schema::hasColumn('projects', 'implemented_solutions')) {
                $table->json('implemented_solutions')->nullable()->after('business_problems');
            }

            if (! Schema::hasColumn('projects', 'implementation_process')) {
                $table->json('implementation_process')->nullable()->after('implemented_solutions');
            }

            if (! Schema::hasColumn('projects', 'achieved_results')) {
                $table->json('achieved_results')->nullable()->after('implementation_process');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            foreach ([
                'achieved_results',
                'implementation_process',
                'implemented_solutions',
                'business_problems',
                'project_overview',
            ] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('faqs') || ! Schema::hasColumn('field_categories', 'faqs')) {
            return;
        }

        DB::table('field_categories')
            ->whereNotNull('faqs')
            ->select(['id', 'faqs'])
            ->orderBy('id')
            ->get()
            ->each(function (object $category): void {
                $items = json_decode((string) $category->faqs, true);

                if (! is_array($items)) {
                    return;
                }

                foreach (array_values($items) as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $question = $item['question'] ?? $item['title'] ?? null;
                    $answer = $item['answer'] ?? $item['description'] ?? $item['content'] ?? null;

                    if (blank($question) && blank($answer)) {
                        continue;
                    }

                    DB::table('faqs')->insert([
                        'faqable_type' => App\Models\FieldCategory::class,
                        'faqable_id' => $category->id,
                        'question' => (string) ($question ?: 'Câu hỏi thường gặp'),
                        'answer' => $answer,
                        'position' => (int) ($item['position'] ?? $index),
                        'status' => (bool) ($item['status'] ?? true),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        Schema::table('field_categories', function (Blueprint $table) {
            if (Schema::hasColumn('field_categories', 'faqs')) {
                $table->dropColumn('faqs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('field_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('field_categories', 'faqs')) {
                $table->json('faqs')->nullable()->after('implementation_steps');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_import_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vendor')->nullable();
            $table->text('description')->nullable();
            $table->json('column_map')->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_import_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('original_filename');
            $table->string('disk')->default('local');
            $table->string('stored_path');
            $table->char('source_hash', 64)->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('total_sheets')->default(0);
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('ready_rows')->default(0);
            $table->unsignedInteger('review_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->unsignedInteger('assets_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('errors')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('product_import_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_import_batch_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sheet_index');
            $table->string('name');
            $table->unsignedInteger('highest_row')->default(0);
            $table->unsignedInteger('highest_column')->default(0);
            $table->string('status')->default('pending')->index();
            $table->json('headings')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['product_import_batch_id', 'sheet_index']);
            $table->index(['product_import_batch_id', 'name']);
        });

        Schema::create('product_import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_import_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_import_sheet_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('raw_cells')->nullable();
            $table->json('normalized')->nullable();
            $table->string('code')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->longText('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('specifications')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('suggested_category_id')->nullable()->references('id')->on('categories')->nullOnDelete();
            $table->string('category_path')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->json('gallery')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('raw')->index();
            $table->json('warnings')->nullable();
            $table->json('errors')->nullable();
            $table->timestamps();

            $table->unique(['product_import_batch_id', 'product_import_sheet_id', 'row_number'], 'product_import_rows_unique_source');
            $table->index(['product_import_batch_id', 'status']);
            $table->index(['category_id', 'status']);
        });

        Schema::create('product_import_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_import_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_import_sheet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_import_row_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('media_id')->nullable()->index();
            $table->string('sheet_name')->nullable();
            $table->string('drawing_name')->nullable();
            $table->string('picture_name')->nullable();
            $table->unsignedInteger('row_number')->nullable()->index();
            $table->unsignedInteger('column_number')->nullable();
            $table->string('coordinate')->nullable();
            $table->string('disk')->default('public');
            $table->string('storage_path');
            $table->string('filename');
            $table->string('ext', 20)->nullable();
            $table->string('mime')->nullable();
            $table->char('hash', 64)->nullable()->index();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->boolean('is_ignored')->default(false)->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['product_import_batch_id', 'row_number', 'column_number'], 'product_import_assets_position_index');
        });

        Schema::create('product_import_category_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_import_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_type')->default('sheet')->index();
            $table->string('source_value');
            $table->string('normalized_value')->index();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category_path')->nullable();
            $table->boolean('auto_create')->default(false);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['product_import_profile_id', 'source_type', 'normalized_value'], 'product_import_category_lookup');
        });

        Schema::create('product_import_errors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->string('brand')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('error_type')->nullable();
            $table->text('error_message')->nullable();
            $table->json('raw_product')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_import_errors');
        Schema::dropIfExists('product_import_category_maps');
        Schema::dropIfExists('product_import_assets');
        Schema::dropIfExists('product_import_rows');
        Schema::dropIfExists('product_import_sheets');
        Schema::dropIfExists('product_import_batches');
        Schema::dropIfExists('product_import_profiles');
    }
};

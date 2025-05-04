<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('csv_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename');
            $table->string('filename');
            $table->string('file_path');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('status_id');
            $table->timestamps();
        });

        Schema::create('product_import_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('csv_import_batch_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('csv_import_batch_id')->references('id')->on('csv_import_batches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_import_batches');
        Schema::dropIfExists('product_import_details');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the sub_categories_descriptions table exists
        if (!Schema::hasTable('sub_categories_descriptions')) {
            Schema::create('sub_categories_descriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id'); // Changed to unsignedBigInteger for consistency
                $table->unsignedBigInteger('language_id'); // Changed to unsignedBigInteger for consistency
                $table->string('name')->nullable();
                $table->timestamps();

                // Optional: Add foreign key constraints if relevant tables exist
                $table->foreign('parent_id')->references('id')->on('sub_categories')->onDelete('cascade');
                $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the sub_categories_descriptions table exists before attempting to drop it
        if (Schema::hasTable('sub_categories_descriptions')) {
            Schema::dropIfExists('sub_categories_descriptions');
        }
    }
};

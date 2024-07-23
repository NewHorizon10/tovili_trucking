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
        // Check if the table already exists
        if (!Schema::hasTable('about_us_descriptions')) {
            Schema::create('about_us_descriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('about_us_id'); // Reference to the about_us table
                $table->unsignedBigInteger('language_id'); // Reference to the language table
                $table->string('title')->nullable(); // Title of the description
                $table->text('description')->nullable(); // Detailed description
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('about_us_id')->references('id')->on('about_us')->onDelete('cascade');
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
        // Optionally, drop the table if it exists
        if (Schema::hasTable('about_us_descriptions')) {
            Schema::dropIfExists('about_us_descriptions');
        }
    }
};

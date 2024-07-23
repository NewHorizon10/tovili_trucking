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
        if (!Schema::hasTable('team_descriptions')) {
            Schema::create('team_descriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_id'); // Reference to the team
                $table->unsignedBigInteger('language_id'); // Reference to the language
                $table->string('title'); // Title or name of the team description
                $table->text('description')->nullable(); // Description text
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
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
        if (Schema::hasTable('team_descriptions')) {
            Schema::dropIfExists('team_descriptions');
        }
    }
};

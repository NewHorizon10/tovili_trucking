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
        if (!Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->string('title'); // Title of the achievement
                $table->text('description')->nullable(); // Description of the achievement
                $table->unsignedBigInteger('user_id')->nullable(); // Optional reference to the user who achieved it
                $table->date('date_achieved'); // Date when the achievement was made
                $table->boolean('is_active')->default(true); // Status of the achievement
                $table->timestamps();

                // Foreign key constraint for user_id if necessary
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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
        if (Schema::hasTable('achievements')) {
            Schema::dropIfExists('achievements');
        }
    }
};

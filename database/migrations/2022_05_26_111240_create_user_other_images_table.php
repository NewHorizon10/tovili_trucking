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
        // Check if the user_other_images table exists
        if (!Schema::hasTable('user_other_images')) {
            Schema::create('user_other_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // Changed to unsignedBigInteger for consistency
                $table->string('gallery_image');
                $table->timestamps();

                // Optional: Add foreign key constraint if there's a users table
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        // Check if the user_other_images table exists before attempting to drop it
        if (Schema::hasTable('user_other_images')) {
            Schema::dropIfExists('user_other_images');
        }
    }
};

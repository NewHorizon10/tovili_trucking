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
        // Check if the userdevicetokens table exists
        if (!Schema::hasTable('userdevicetokens')) {
            Schema::create('userdevicetokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // Changed to unsignedBigInteger for consistency
                $table->string('device_type');
                $table->string('device_id');
                $table->string('device_token');
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
        // Check if the userdevicetokens table exists before attempting to drop it
        if (Schema::hasTable('userdevicetokens')) {
            Schema::dropIfExists('userdevicetokens');
        }
    }
};

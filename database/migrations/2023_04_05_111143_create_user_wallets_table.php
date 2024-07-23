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
        if (!Schema::hasTable('user_wallets')) {
            Schema::create('user_wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->double('available_balance', 15, 2)->default(0); // Changed to double with precision
                $table->double('reserved_balance', 15, 2)->default(0); // Changed to double with precision
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Corrected foreign key syntax
                $table->timestamps();
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
        if (Schema::hasTable('user_wallets')) {
            Schema::dropIfExists('user_wallets');
        }
    }
};

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
        if (!Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Client's name
                $table->string('email')->unique(); // Client's email, must be unique
                $table->string('phone_number')->nullable(); // Client's phone number, optional
                $table->text('address')->nullable(); // Client's address, optional
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
        if (Schema::hasTable('clients')) {
            Schema::dropIfExists('clients');
        }
    }
};

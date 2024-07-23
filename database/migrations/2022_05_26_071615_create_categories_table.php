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
        // Check if the categories table exists
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('image')->nullable();
                $table->integer('is_active')->default(1); // Changed to integer type and default value
                $table->integer('is_deleted')->default(0); // Changed to integer type and default value
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
        // Check if the categories table exists before attempting to drop it
        if (Schema::hasTable('categories')) {
            Schema::dropIfExists('categories');
        }
    }
};

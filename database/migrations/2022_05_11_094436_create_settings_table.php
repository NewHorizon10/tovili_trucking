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
        // Check if the settings table exists
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->nullable();
                $table->text('value')->nullable();
                $table->string('title')->nullable();
                $table->string('description')->nullable();
                $table->text('input_type')->nullable();
                $table->integer('editable')->nullable();
                $table->string('weight')->nullable();
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
        // Check if the settings table exists before attempting to drop it
        if (Schema::hasTable('settings')) {
            Schema::dropIfExists('settings');
        }
    }
};

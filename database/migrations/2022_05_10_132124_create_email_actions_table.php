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
        // Check if the email_actions table exists
        if (!Schema::hasTable('email_actions')) {
            Schema::create('email_actions', function (Blueprint $table) {
                $table->id();
                $table->string('action')->nullable();
                $table->string('options')->nullable();
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
        // No action needed if the table already exists
    }
};

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
        // Check if the email_template_descriptions table exists
        if (!Schema::hasTable('email_template_descriptions')) {
            Schema::create('email_template_descriptions', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->integer('language_id')->nullable();
                $table->string('name')->nullable();
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
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
        // Check if the email_template_descriptions table exists before attempting to drop it
        if (Schema::hasTable('email_template_descriptions')) {
            Schema::dropIfExists('email_template_descriptions');
        }
    }
};

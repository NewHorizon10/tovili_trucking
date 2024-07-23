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
        // Check if the faq_descriptions table exists
        if (!Schema::hasTable('faq_descriptions')) {
            Schema::create('faq_descriptions', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->integer('language_id')->nullable();
                $table->text('question')->nullable();
                $table->text('answer')->nullable();
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
        // Check if the faq_descriptions table exists before attempting to drop it
        if (Schema::hasTable('faq_descriptions')) {
            Schema::dropIfExists('faq_descriptions');
        }
    }
};

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
        // Check if the cms_descriptions table exists
        if (!Schema::hasTable('cms_descriptions')) {
            Schema::create('cms_descriptions', function (Blueprint $table) {
                $table->id();
                $table->string('parent_id')->nullable();
                $table->string('language_id')->nullable();
                $table->string('title')->nullable();
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
        // No action needed if the table already exists
    }
};

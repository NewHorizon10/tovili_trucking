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
        if (!Schema::hasTable('blog_descriptions')) {
            Schema::create('blog_descriptions', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id');
                $table->integer('language_id');
                $table->string('page_name')->nullable();
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
        // Optionally, drop the table if it exists
        if (Schema::hasTable('blog_descriptions')) {
            Schema::dropIfExists('blog_descriptions');
        }
    }
};

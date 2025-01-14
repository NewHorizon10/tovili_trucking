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
        // Check if the faqs table exists
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->text('question')->nullable();
                $table->text('answer')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('faq_order')->nullable();
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
        // Check if the faqs table exists before attempting to drop it
        if (Schema::hasTable('faqs')) {
            Schema::dropIfExists('faqs');
        }
    }
};

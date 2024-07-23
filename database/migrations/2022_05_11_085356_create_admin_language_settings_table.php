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
        // Check if the admin_language_settings table exists
        if (!Schema::hasTable('admin_language_settings')) {
            Schema::create('admin_language_settings', function (Blueprint $table) {
                $table->id();
                $table->string('msgid')->nullable();
                $table->string('locale')->nullable();
                $table->string('msgstr')->nullable();
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
        // Check if the admin_language_settings table exists before attempting to drop it
        if (Schema::hasTable('admin_language_settings')) {
            Schema::dropIfExists('admin_language_settings');
        }
    }
};

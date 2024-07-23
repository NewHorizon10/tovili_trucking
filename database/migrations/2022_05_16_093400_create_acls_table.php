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
        // Check if the acls table exists
        if (!Schema::hasTable('acls')) {
            Schema::create('acls', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->string('title')->nullable();
                $table->text('path')->nullable();
                $table->text('icon')->nullable();
                $table->integer('module_order')->nullable();
                $table->integer('is_active')->default(1); // Changed default to integer value
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
        // Check if the acls table exists before attempting to drop it
        if (Schema::hasTable('acls')) {
            Schema::dropIfExists('acls');
        }
    }
};

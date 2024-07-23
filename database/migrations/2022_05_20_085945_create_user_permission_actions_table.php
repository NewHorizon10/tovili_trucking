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
        // Check if the user_permission_actions table exists
        if (!Schema::hasTable('user_permission_actions')) {
            Schema::create('user_permission_actions', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('user_permission_id')->nullable();
                $table->integer('admin_module_id')->nullable();
                $table->integer('admin_sub_module_id')->nullable();
                $table->integer('admin_module_action_id')->nullable();
                $table->integer('is_active')->default(1); // Added default value
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
        // Check if the user_permission_actions table exists before attempting to drop it
        if (Schema::hasTable('user_permission_actions')) {
            Schema::dropIfExists('user_permission_actions');
        }
    }
};

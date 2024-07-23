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
        // Check if the designation_permissions table exists
        if (!Schema::hasTable('designation_permissions')) {
            Schema::create('designation_permissions', function (Blueprint $table) {
                $table->id();
                $table->integer('designation_id')->nullable();
                $table->integer('admin_module_id')->nullable();
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
        // Check if the designation_permissions table exists before attempting to drop it
        if (Schema::hasTable('designation_permissions')) {
            Schema::dropIfExists('designation_permissions');
        }
    }
};
